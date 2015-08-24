<?php
namespace In2code\Powermail\Controller;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\SendParametersService;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\LocalizationUtility;
use In2code\Powermail\Utility\OptinUtility;
use In2code\Powermail\Utility\SaveToAnyTableUtility;
use In2code\Powermail\Utility\SessionUtility;
use In2code\Powermail\Utility\StringUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Controller for powermail forms
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class FormController extends AbstractController {

	/**
	 * @var \In2code\Powermail\Domain\Service\SendMailService
	 * @inject
	 */
	protected $sendMailService;

	/**
	 * action show form for creating new mails
	 *
	 * @return void
	 */
	public function formAction() {
		if (empty($this->settings['main']['form'])) {
			return;
		}
		$forms = $this->formRepository->findByUids($this->settings['main']['form']);
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', array($forms, $this));
		SessionUtility::saveFormStartInSession($forms, $this->settings);

		$this->assignForAll();
		$this->view->assignMultiple(
			array(
				'forms' => $forms,
				'messageClass' => $this->messageClass,
				'action' => ($this->settings['main']['confirmation'] ? 'confirmation' : 'create')
			)
		);
	}

	/**
	 * Rewrite Arguments to receive a clean mail object in createAction
	 *
	 * @return void
	 */
	public function initializeCreateAction() {
		$this->reformatParamsForAction();
		$this->forwardIfFormParamsDoNotMatch();
	}

	/**
	 * Action create entry
	 *
	 * @param Mail $mail
	 * @param string $hash
	 * @validate $mail In2code\Powermail\Domain\Validator\UploadValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\InputValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\PasswordValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\CaptchaValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\SpamShieldValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\UniqueValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\CustomValidator
	 * @required $mail
	 * @return void
	 */
	public function createAction(Mail $mail, $hash = NULL) {
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', array($mail, $hash, $this));
		BasicFileUtility::fileUpload($this->settings['misc']['file']['folder'], $this->settings['misc']['file']['extension'], $mail);
		SessionUtility::saveSessionValuesForPrefill($mail, $this->settings);

		if ($this->settings['debug']['variables']) {
			GeneralUtility::devLog('Variables', $this->extensionName, 0, GeneralUtility::_POST());
		}
		if ($this->isMailPersistActive($hash)) {
			$this->saveMail($mail);
			$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'AfterMailDbSaved', array($mail, $this));
		}
		if ($this->isSendMailActive($mail, $hash)) {
			$this->sendMailPreflight($mail, $hash);
			SaveToAnyTableUtility::preflight($mail, $this->conf);
			/** @var SendParametersService $sendParametersService */
			$sendParameters = $this->objectManager->get('In2code\\Powermail\\Domain\\Service\\SendParametersService');
			$sendParameters->sendFromConfiguration($mail, $this->conf);
		} else {
			$this->sendConfirmationMail($mail);
			$this->view->assign('optinActive', TRUE);
		}
		if ($this->settings['db']['enable']) {
			$this->mailRepository->update($mail);
			$this->persistenceManager->persistAll();
		}

		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'AfterSubmitView', array($mail, $hash, $this));
		$this->assignForAll();
		$this->showThx($mail);
	}

	/**
	 * Rewrite Arguments to receive a clean mail object in confirmationAction
	 *
	 * @return void
	 */
	public function initializeConfirmationAction() {
		$this->reformatParamsForAction();
		$this->forwardIfFormParamsDoNotMatch();
	}

	/**
	 * Show Confirmation message after submit (if view is activated)
	 *
	 * @param Mail $mail
	 * @validate $mail In2code\Powermail\Domain\Validator\UploadValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\InputValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\PasswordValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\CaptchaValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\SpamShieldValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\UniqueValidator
	 * @validate $mail In2code\Powermail\Domain\Validator\CustomValidator
	 * @required $mail
	 * @return void
	 */
	public function confirmationAction(Mail $mail) {
		BasicFileUtility::fileUpload($this->settings['misc']['file']['folder'], $this->settings['misc']['file']['extension'], $mail);
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', array($mail, $this));
		$this->showThx($mail);
	}

	/**
	 * Choose where to send Mails
	 *
	 * @param Mail $mail
	 * @param string $hash
	 * @return void
	 */
	protected function sendMailPreflight(Mail $mail, $hash = NULL) {
		if ($this->settings['sender']['enable'] && $this->mailRepository->getSenderMailFromArguments($mail)) {
			$this->sendSenderMail($mail);
		}
		if ($this->settings['receiver']['enable']) {
			$this->sendReceiverMail($mail, $hash);
		}
	}

	/**
	 * Mail Generation for Receiver
	 *
	 * @param Mail $mail
	 * @param string $hash
	 * @return void
	 */
	protected function sendReceiverMail(Mail $mail, $hash = NULL) {
		$receiverString = TemplateUtility::fluidParseString(
			$this->settings['receiver']['email'],
			$this->mailRepository->getVariablesWithMarkersFromMail($mail)
		);
		TypoScriptUtility::overwriteValueFromTypoScript($receiverString, $this->conf['receiver.']['overwrite.'], 'email');
		$receivers = StringUtility::getReceiverEmails($receiverString, $this->settings['receiver']['fe_group']);
		$mail->setReceiverMail(implode("\n", $receivers));
		TypoScriptUtility::overwriteValueFromTypoScript($defaultSenderEmail, $this->conf['receiver.']['default.'], 'senderEmail');
		TypoScriptUtility::overwriteValueFromTypoScript($defaultSenderName, $this->conf['receiver.']['default.'], 'senderName');
		foreach ($receivers as $receiver) {
			$email = array(
				'template' => 'Mail/ReceiverMail',
				'receiverEmail' => $receiver,
				'receiverName' => $this->settings['receiver']['name'] ? $this->settings['receiver']['name'] : 'Powermail',
				'senderEmail' => $this->mailRepository->getSenderMailFromArguments($mail, $defaultSenderEmail),
				'senderName' => $this->mailRepository->getSenderNameFromArguments($mail, $defaultSenderName),
				'subject' => $this->settings['receiver']['subject'],
				'rteBody' => $this->settings['receiver']['body'],
				'format' => $this->settings['receiver']['mailformat'],
				'variables' => array(
					'hash' => $hash
				)
			);
			TypoScriptUtility::overwriteValueFromTypoScript($email['receiverName'], $this->conf['receiver.']['overwrite.'], 'name');
			TypoScriptUtility::overwriteValueFromTypoScript($email['senderName'], $this->conf['receiver.']['overwrite.'], 'senderName');
			TypoScriptUtility::overwriteValueFromTypoScript($email['senderEmail'], $this->conf['receiver.']['overwrite.'], 'senderEmail');
			$sent = $this->sendMailService->sendEmailPreflight($email, $mail, $this->settings, 'receiver');

			if (!$sent) {
				$this->addFlashMessage(
					LocalizationUtility::translate('error_mail_not_created'),
					'',
					AbstractMessage::ERROR
				);
				$this->messageClass = 'error';
			}
		}
	}

	/**
	 * Mail Generation for Sender
	 *
	 * @param Mail $mail
	 * @return void
	 */
	protected function sendSenderMail(Mail $mail) {
		$email = array(
			'template' => 'Mail/SenderMail',
			'receiverName' => $this->mailRepository->getSenderNameFromArguments($mail, 'Powermail'),
			'receiverEmail' => $this->mailRepository->getSenderMailFromArguments($mail),
			'senderName' => $this->settings['sender']['name'],
			'senderEmail' => $this->settings['sender']['email'],
			'subject' => $this->settings['sender']['subject'],
			'rteBody' => $this->settings['sender']['body'],
			'format' => $this->settings['sender']['mailformat']
		);
		TypoScriptUtility::overwriteValueFromTypoScript($email['receiverEmail'], $this->conf['sender.']['overwrite.'], 'email');
		TypoScriptUtility::overwriteValueFromTypoScript($email['receiverName'], $this->conf['sender.']['overwrite.'], 'name');
		TypoScriptUtility::overwriteValueFromTypoScript($email['senderName'], $this->conf['sender.']['overwrite.'], 'senderName');
		TypoScriptUtility::overwriteValueFromTypoScript($email['senderEmail'], $this->conf['sender.']['overwrite.'], 'senderEmail');
		$this->sendMailService->sendEmailPreflight($email, $mail, $this->settings, 'sender');
	}

	/**
	 * Send Optin Confirmation Mail to user
	 *
	 * @param Mail $mail
	 * @return void
	 */
	protected function sendConfirmationMail(Mail &$mail) {
		$email = array(
			'template' => 'Mail/OptinMail',
			'receiverName' => $this->mailRepository->getSenderNameFromArguments($mail) ?
				$this->mailRepository->getSenderNameFromArguments($mail) : 'Powermail',
			'receiverEmail' => $this->mailRepository->getSenderMailFromArguments($mail),
			'senderName' => $this->settings['sender']['name'],
			'senderEmail' => $this->settings['sender']['email'],
			'subject' => $this->cObj->cObjGetSingle($this->conf['optin.']['subject'], $this->conf['optin.']['subject.']),
			'rteBody' => '',
			'format' => $this->settings['sender']['mailformat'],
			'variables' => array(
				'hash' => OptinUtility::createOptinHash($mail),
				'mail' => $mail
			)
		);
		TypoScriptUtility::overwriteValueFromTypoScript($email['receiverName'], $this->conf['optin.']['overwrite.'], 'name');
		TypoScriptUtility::overwriteValueFromTypoScript($email['receiverEmail'], $this->conf['optin.']['overwrite.'], 'email');
		TypoScriptUtility::overwriteValueFromTypoScript($email['senderName'], $this->conf['optin.']['overwrite.'], 'senderName');
		TypoScriptUtility::overwriteValueFromTypoScript($email['senderEmail'], $this->conf['optin.']['overwrite.'], 'senderEmail');
		$this->sendMailService->sendEmailPreflight($email, $mail, $this->settings, 'optin');
	}

	/**
	 * Show THX message after submit
	 *
	 * @param Mail $mail
	 * @return void
	 */
	protected function showThx(Mail $mail) {
		$this->redirectToTarget();

		// assign
		$this->view->assign('mail', $mail);
		$this->view->assign('marketingInfos', SessionUtility::getMarketingInfos());
		$this->view->assign('messageClass', $this->messageClass);
		$this->view->assign('powermail_rte', $this->settings['thx']['body']);

		// get variable array
		$variablesWithMarkers = $this->mailRepository->getVariablesWithMarkersFromMail($mail);
		$this->view->assign('variablesWithMarkers', ArrayUtility::htmlspecialcharsOnArray($variablesWithMarkers));
		$this->view->assignMultiple($variablesWithMarkers);
		$this->view->assignMultiple($this->mailRepository->getLabelsWithMarkersFromMail($mail));

		// powermail_all
		$this->view->assign('powermail_all', TemplateUtility::powermailAll($mail, 'web', $this->settings, $this->actionMethodName));
	}

	/**
	 * Redirect on thx action
	 *
	 * @return void
	 */
	protected function redirectToTarget() {
		$redirectTargetUri = $this->getRedirectTargetUri();
		if (
			!empty($redirectTargetUri) &&
			$this->request->getControllerActionName() !== 'confirmation' &&
			!(!empty($this->settings['main']['optin']) && empty($this->piVars['hash']))
		) {
			$this->redirectToUri($redirectTargetUri);
		}
	}

	/**
	 * Save mail on submit
	 *
	 * @param Mail $mail
	 * @return void
	 */
	protected function saveMail(Mail &$mail = NULL) {
		$marketingInfos = SessionUtility::getMarketingInfos();
		$mail
			->setPid(FrontendUtility::getStoragePage($this->settings['main']['pid']))
			->setSenderMail($this->mailRepository->getSenderMailFromArguments($mail))
			->setSenderName($this->mailRepository->getSenderNameFromArguments($mail))
			->setSubject($this->settings['receiver']['subject'])
			->setReceiverMail($this->settings['receiver']['email'])
			->setBody(DebugUtility::viewArray($this->mailRepository->getVariablesWithMarkersFromMail($mail)))
			->setSpamFactor(SessionUtility::getSpamFactorFromSession())
			->setTime((time() - SessionUtility::getFormStartFromSession($mail->getForm()->getUid(), $this->settings)))
			->setUserAgent(GeneralUtility::getIndpEnv('HTTP_USER_AGENT'))
			->setMarketingRefererDomain($marketingInfos['refererDomain'])
			->setMarketingReferer($marketingInfos['referer'])
			->setMarketingCountry($marketingInfos['country'])
			->setMarketingMobileDevice($marketingInfos['mobileDevice'])
			->setMarketingFrontendLanguage($marketingInfos['frontendLanguage'])
			->setMarketingBrowserLanguage($marketingInfos['browserLanguage'])
			->setMarketingPageFunnel($marketingInfos['pageFunnel']);
		if (FrontendUtility::isLoggedInFrontendUser()) {
			$mail->setFeuser(
				$this->userRepository->findByUid(FrontendUtility::getPropertyFromLoggedInFrontendUser('uid'))
			);
		}
		if (!ConfigurationUtility::isDisableIpLogActive()) {
			$mail->setSenderIp(GeneralUtility::getIndpEnv('REMOTE_ADDR'));
		}
		if ($this->settings['main']['optin'] || $this->settings['db']['hidden']) {
			$mail->setHidden(TRUE);
		}
		foreach ($mail->getAnswers() as $answer) {
			$answer->setPid(FrontendUtility::getStoragePage($this->settings['main']['pid']));
		}
		$this->mailRepository->add($mail);
		$this->persistenceManager->persistAll();
	}

	/**
	 * Confirm Double Optin
	 *
	 * @param int $mail
	 * @param string $hash Given Hash String
	 * @return void
	 */
	public function optinConfirmAction($mail, $hash) {
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', array($mail, $hash, $this));
		$mail = $this->mailRepository->findByUid($mail);

		if ($mail !== NULL && OptinUtility::checkOptinHash($hash, $mail)) {
			if ($mail->getHidden()) {
				$mail->setHidden(FALSE);
				$this->mailRepository->update($mail);
				$this->persistenceManager->persistAll();

				$this->forward('create', NULL, NULL, array('mail' => $mail, 'hash' => $hash));
			}
		}
	}

	/**
	 * Marketing Tracking Action
	 *
	 * @param string $referer Referer
	 * @param int $language Frontend Language Uid
	 * @param int $pid Page Id
	 * @param int $mobileDevice Is mobile device?
	 * @return void
	 */
	public function marketingAction($referer = NULL, $language = 0, $pid = 0, $mobileDevice = 0) {
		SessionUtility::storeMarketingInformation($referer, $language, $pid, $mobileDevice);
	}

	/**
	 * Initializes this object
	 *
	 * @return void
	 */
	public function initializeObject() {
		$this->cObj = $this->configurationManager->getContentObject();
		$typoScriptSetup = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
		);
		$this->conf = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
		ConfigurationUtility::mergeTypoScript2FlexForm($this->settings);

		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'Settings', array($this));

		if ($this->settings['debug']['settings']) {
			GeneralUtility::devLog(
				'Settings',
				$this->extensionName,
				0,
				$this->settings
			);
		}
	}

	/**
	 * Initialize Action
	 *
	 * @return void
	 */
	public function initializeAction() {
		parent::initializeAction();

		if (!isset($this->settings['staticTemplate'])) {
			$this->controllerContext = $this->buildControllerContext();
			$this->addFlashMessage(
				LocalizationUtility::translate('error_no_typoscript'),
				'',
				AbstractMessage::ERROR
			);
		}
	}

	/**
	 * Forward to form action if wrong form in plugin variables given
	 *
	 * @return void
	 */
	protected function forwardIfFormParamsDoNotMatch() {
		$arguments = $this->request->getArguments();
		$formsToContent = GeneralUtility::intExplode(',', $this->settings['main']['form']);
		if (is_array($arguments['mail']) && !in_array($arguments['mail']['form'], $formsToContent)) {
			$this->forward('form');
		}
	}

	/**
	 * Decide if the mail object should be persisted or not
	 * 		persist if
	 * 			- enabled with TypoScript AND hash is not set OR
	 * 			- optin is enabled AND hash is not set (even if disabled in TS)
	 *
	 * @param string $hash
	 * @return bool
	 */
	protected function isMailPersistActive($hash) {
		return (!empty($this->settings['db']['enable']) || !empty($this->settings['main']['optin'])) && $hash === NULL;
	}

	/**
	 * Check if mail should be send
	 * 		send when
	 * 			- optin is deaktivated OR
	 * 			- optin is active AND hash is correct
	 *
	 * @param Mail $mail
	 * @param string $hash
	 * @return bool
	 */
	protected function isSendMailActive(Mail $mail, $hash) {
		return empty($this->settings['main']['optin']) || (!empty($this->settings['main']['optin'])
			&& OptinUtility::checkOptinHash($hash, $mail));
	}
}