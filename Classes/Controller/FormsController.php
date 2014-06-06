<?php

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
 *
 */
class Tx_Powermail_Controller_FormsController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * formsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FormsRepository
	 */
	protected $formsRepository;

	/**
	 * mailsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_MailsRepository
	 */
	protected $mailsRepository;

	/**
	 * answersRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_AnswersRepository
	 */
	protected $answersRepository;

	/**
	 * @var Tx_Extbase_SignalSlot_Dispatcher
	 */
	protected $signalSlotDispatcher;

	/**
	 * cObj
	 *
	 * @var Content Object
	 */
	protected $cObj;

	/**
	 * TypoScript configuration
	 *
	 * @var configuration
	 */
	protected $conf;

	/**
	 * Instance for Misc Functions
	 *
	 * @var Tx_Powermail_Utility_Div
	 */
	protected $div;

	/**
	 * meesage Classe
	 *
	 * @var string
	 */
	protected $messageClass = 'error';

	/**
	 * action show form for creating new mails
	 *
	 * @return void
	 */
	public function formAction() {
		if (!isset($this->settings['main']['form']) || !$this->settings['main']['form']) {
			return;
		}

		// get forms
		$forms = $this->formsRepository->findByUids($this->settings['main']['form']);
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', array($forms, $this));
		$this->view->assign('forms', $forms);
		$this->view->assign('messageClass', $this->messageClass);
		$this->view->assign('action', ($this->settings['main']['confirmation'] ? 'confirmation' : 'create'));

		// open session
		if (method_exists($forms->getFirst(), 'getUid')) {
			Tx_Powermail_Utility_Div::saveFormStartInSession($forms->getFirst()->getUid());
		}
	}

	/**
	 * Show Confirmation message after submit (if view is activated)
	 *
	 * @param array $field Field values
	 * @param integer $form Form UID
	 * @validate $field Tx_Powermail_Domain_Validator_UploadValidator
	 * @validate $field Tx_Powermail_Domain_Validator_MandatoryValidator
	 * @validate $field Tx_Powermail_Domain_Validator_StringValidator
	 * @validate $field Tx_Powermail_Domain_Validator_CaptchaValidator
	 * @validate $field Tx_Powermail_Domain_Validator_SpamShieldValidator
	 * @validate $field Tx_Powermail_Domain_Validator_CustomValidator
	 * @return void
	 */
	public function confirmationAction(array $field = array(), $form = NULL) {
		// forward back to formAction if wrong form
		$this->ignoreWrongForm($form);
		// add upload fields
		Tx_Powermail_Utility_Div::addUploadsToFields($field);
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', array($field, $form, $this));
		$this->view->assign('field', $field);
		$this->view->assign('form', $form);

		// markers
		$variablesWithMarkers = $this->div->getVariablesWithMarkers($field);
		$this->view->assignMultiple($variablesWithMarkers);

		// powermail_all
		$variables = $this->div->getVariablesWithLabels($field);
		$content = $this->div->powermailAll($variables, $this->configurationManager, $this->objectManager, 'web', $this->settings);
		$this->view->assign('powermail_all', $content);
	}

	/**
	 * Action create entry
	 *
	 * @param integer $form Form UID
	 * @param array $field Field Values
	 * @param integer $mail Mail object (normally empty, used for double-optin)
	 * @validate $field Tx_Powermail_Domain_Validator_UploadValidator
	 * @validate $field Tx_Powermail_Domain_Validator_MandatoryValidator
	 * @validate $field Tx_Powermail_Domain_Validator_StringValidator
	 * @validate $field Tx_Powermail_Domain_Validator_CaptchaValidator
	 * @validate $field Tx_Powermail_Domain_Validator_SpamShieldValidator
	 * @validate $field Tx_Powermail_Domain_Validator_CustomValidator
	 * @validate $form notEmpty
	 * @dontvalidate $mail
	 * @return void
	 */
	public function createAction($form, array $field = array(), $mail = NULL) {
		// forward to formAction if wrong form - relevant if there are more forms
		$this->ignoreWrongForm($form);

		// add uploaded files to $field
		Tx_Powermail_Utility_Div::addUploadsToFields($field);
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', array($field, $form, $mail, $this));

		// Debug Output
		if ($this->settings['debug']['variables']) {
			t3lib_utility_Debug::debug($field, 'powermail debug: Show Variables');
		}

		// Save Mail to DB
		if ($this->settings['db']['enable'] && !$mail) {
			$dbField = $this->div->rewriteDateInFields($field);
			$newMail = $this->saveMail($dbField, $form);
			$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'AfterMailDbSaved', array($field, $form, $mail, $this));
		}

		if (!$this->settings['main']['optin'] || ($this->settings['main']['optin'] && $mail)) {
			// Send Mail to receivers
			$this->sendMail($field);

			// Save to other tables
			$saveToTable = $this->objectManager->get('Tx_Powermail_Utility_SaveToTable');
			$saveToTable->main($this->div->getVariablesWithMarkers($field), $this->conf, $this->cObj);

			// Powermail sendpost
			$this->div->sendPost($field, $this->conf, $this->configurationManager);

			// Some output stuff
			$this->showThx($field);
		} else {
			$this->sendConfirmationMail($field, $newMail);
		}

		$this->signalSlotDispatcher->dispatch(
			__CLASS__, __FUNCTION__ . 'AfterSubmitView', array($field, $form, $mail, $this, $newMail)
		);
		$this->view->assign('optinActive', (!$this->settings['main']['optin'] || ($this->settings['main']['optin'] && $mail) ? 0 : 1));
	}

	/**
	 * Send Mails
	 *
	 * @param array $field
	 * @return void
	 */
	protected function sendMail($field) {
		if ($this->settings['receiver']['enable']) {
			$receiverString = $this->div->fluidParseString(
				$this->settings['receiver']['email'],
				$this->objectManager,
				$this->div->getVariablesWithMarkers($field)
			);
			$receivers = $this->div->getReceiverEmails($receiverString, $this->settings['receiver']['fe_group']);
			if ($this->cObj->cObjGetSingle(
				$this->conf['receiver.']['overwrite.']['email'],
				$this->conf['receiver.']['overwrite.']['email.'])
			) {
				$receivers = t3lib_div::trimExplode(
					',',
					$this->cObj->cObjGetSingle(
						$this->conf['receiver.']['overwrite.']['email'],
						$this->conf['receiver.']['overwrite.']['email.']
					),
					TRUE
				);
			}
			foreach ($receivers as $receiver) {
				$mail = array();
				$mail['receiverName'] = $this->settings['receiver']['name'] ? $this->settings['receiver']['name'] : 'Powermail';
				$mail['receiverEmail'] = $receiver;
				$mail['senderName'] = $this->div->getSenderNameFromArguments($field);
				$mail['senderEmail'] = $this->div->getSenderMailFromArguments($field);
				$mail['subject'] = $this->settings['receiver']['subject'];
				$mail['template'] = 'Mails/ReceiverMail';
				$mail['rteBody'] = $this->settings['receiver']['body'];
				$mail['format'] = $this->settings['receiver']['mailformat'];
				if ($this->cObj->cObjGetSingle($this->conf['receiver.']['overwrite.']['name'], $this->conf['receiver.']['overwrite.']['name.'])) { // overwrite from typoscript
					$mail['receiverName'] = $this->cObj->cObjGetSingle($this->conf['receiver.']['overwrite.']['name'], $this->conf['receiver.']['overwrite.']['name.']);
				}
				if ($this->cObj->cObjGetSingle($this->conf['receiver.']['overwrite.']['senderName'], $this->conf['receiver.']['overwrite.']['senderName.'])) { // overwrite from typoscript
					$mail['senderName'] = $this->cObj->cObjGetSingle($this->conf['receiver.']['overwrite.']['senderName'], $this->conf['receiver.']['overwrite.']['senderName.']);
				}
				if ($this->cObj->cObjGetSingle($this->conf['receiver.']['overwrite.']['senderEmail'], $this->conf['receiver.']['overwrite.']['senderEmail.'])) { // overwrite from typoscript
					$mail['senderEmail'] = $this->cObj->cObjGetSingle($this->conf['receiver.']['overwrite.']['senderEmail'], $this->conf['receiver.']['overwrite.']['senderEmail.']);
				}
				$sent = $this->div->sendTemplateEmail($mail, $field, $this->settings, 'receiver', $this->objectManager, $this->configurationManager);

				if (!$sent) {
					$this->flashMessageContainer->add(Tx_Extbase_Utility_Localization::translate('error_mail_not_created', 'powermail'));
					$this->messageClass = 'error';
				}
			}
		}

		// Send Mail to sender
		if ($this->settings['sender']['enable'] && $this->div->getSenderMailFromArguments($field)) {
			$mail = array();
			$mail['receiverName'] = $this->div->getSenderNameFromArguments($field) ? $this->div->getSenderNameFromArguments($field) : 'Powermail';
			$mail['receiverEmail'] = $this->div->getSenderMailFromArguments($field);
			$mail['senderName'] = $this->settings['sender']['name'];
			$mail['senderEmail'] = $this->settings['sender']['email'];
			$mail['subject'] = $this->settings['sender']['subject'];
			$mail['template'] = 'Mails/SenderMail';
			$mail['rteBody'] = $this->settings['sender']['body'];
			$mail['format'] = $this->settings['sender']['mailformat'];
			if ($this->cObj->cObjGetSingle($this->conf['sender.']['overwrite.']['email'], $this->conf['sender.']['overwrite.']['email.'])) { // overwrite from typoscript
				$mail['receiverEmail'] = $this->cObj->cObjGetSingle($this->conf['sender.']['overwrite.']['email'], $this->conf['sender.']['overwrite.']['email.']);
			}
			if ($this->cObj->cObjGetSingle($this->conf['sender.']['overwrite.']['name'], $this->conf['sender.']['overwrite.']['name.'])) { // overwrite from typoscript
				$mail['receiverName'] = $this->cObj->cObjGetSingle($this->conf['sender.']['overwrite.']['name'], $this->conf['sender.']['overwrite.']['name.']);
			}
			if ($this->cObj->cObjGetSingle($this->conf['sender.']['overwrite.']['senderName'], $this->conf['sender.']['overwrite.']['senderName.'])) { // overwrite from typoscript
				$mail['senderName'] = $this->cObj->cObjGetSingle($this->conf['sender.']['overwrite.']['senderName'], $this->conf['sender.']['overwrite.']['senderName.']);
			}
			if ($this->cObj->cObjGetSingle($this->conf['sender.']['overwrite.']['senderEmail'], $this->conf['sender.']['overwrite.']['senderEmail.'])) { // overwrite from typoscript
				$mail['senderEmail'] = $this->cObj->cObjGetSingle($this->conf['sender.']['overwrite.']['senderEmail'], $this->conf['sender.']['overwrite.']['senderEmail.']);
			}
			$this->div->sendTemplateEmail($mail, $field, $this->settings, 'sender', $this->objectManager, $this->configurationManager);
		}
	}

	/**
	 * Send Optin Confirmation Mail
	 *
	 * @param array $field array with field values
	 * @param Tx_Powermail_Domain_Model_Mails $newMail new mail object from db
	 * @return void
	 */
	protected function sendConfirmationMail($field, $newMail) {
		// Send Mail to sender
		$mail = array();
		$mail['receiverName'] = 'Powermail';
		if ($this->div->getSenderNameFromArguments($field)) {
			$mail['receiverName'] = $this->div->getSenderNameFromArguments($field);
		}
		if ($this->cObj->cObjGetSingle($this->conf['optin.']['overwrite.']['name'], $this->conf['optin.']['overwrite.']['name.'])) { // overwrite from typoscript
			$mail['receiverName'] = $this->cObj->cObjGetSingle($this->conf['optin.']['overwrite.']['name'], $this->conf['optin.']['overwrite.']['name.']);
		}
		$mail['receiverEmail'] = $this->div->getSenderMailFromArguments($field);
		if ($this->cObj->cObjGetSingle($this->conf['optin.']['overwrite.']['email'], $this->conf['optin.']['overwrite.']['email.'])) { // overwrite from typoscript
			$mail['receiverEmail'] = $this->cObj->cObjGetSingle($this->conf['optin.']['overwrite.']['email'], $this->conf['optin.']['overwrite.']['email.']);
		}
		$mail['senderName'] = $this->settings['sender']['name'];
		if ($this->cObj->cObjGetSingle($this->conf['optin.']['overwrite.']['senderName'], $this->conf['optin.']['overwrite.']['senderName.'])) { // overwrite from typoscript
			$mail['senderName'] = $this->cObj->cObjGetSingle($this->conf['optin.']['overwrite.']['senderName'], $this->conf['optin.']['overwrite.']['senderName.']);
		}
		$mail['senderEmail'] = $this->settings['sender']['email'];
		if ($this->cObj->cObjGetSingle($this->conf['optin.']['overwrite.']['senderEmail'], $this->conf['optin.']['overwrite.']['senderEmail.'])) { // overwrite from typoscript
			$mail['senderEmail'] = $this->cObj->cObjGetSingle($this->conf['optin.']['overwrite.']['senderEmail'], $this->conf['optin.']['overwrite.']['senderEmail.']);
		}
		$mail['subject'] = $this->cObj->cObjGetSingle($this->conf['optin.']['subject'], $this->conf['optin.']['subject.']);
		$mail['template'] = 'Mails/OptinMail';
		$mail['rteBody'] = '';
		$mail['format'] = $this->settings['sender']['mailformat'];
		$mail['variables'] = array(
			'optinHash' => Tx_Powermail_Utility_Div::createOptinHash($newMail->getUid() . $newMail->getPid() . $newMail->getForm()),
			'mail' => $newMail->getUid()
		);
		$this->div->sendTemplateEmail($mail, $field, $this->settings, 'optin', $this->objectManager, $this->configurationManager);
	}

	/**
	 * Show THX message after submit
	 *
	 * @param array $field
	 * @return void
	 */
	protected function showThx($field) {
		$this->redirectToTarget();

		// assign
		$this->view->assign('marketingInfos', Tx_Powermail_Utility_Div::getMarketingInfos());
		$this->view->assign('messageClass', $this->messageClass);
		$this->view->assign('powermail_rte', $this->settings['thx']['body']);

		// get variable array
		$variablesWithMarkers = $this->div->getVariablesWithMarkers($field);
		$this->view->assign('variablesWithMarkers', $this->div->htmlspecialcharsOnArray($variablesWithMarkers));
		$this->view->assignMultiple($variablesWithMarkers);
		$variablesWithLabels = $this->div->getVariablesWithLabels($field);
		$this->view->assign('variablesWithLabels', $variablesWithLabels);

		// powermail_all
		$variables = $this->div->getVariablesWithLabels($field);
		$content = $this->div->powermailAll($variables, $this->configurationManager, $this->objectManager, 'web', $this->settings);
		$this->view->assign('powermail_all', $content);
	}

	/**
	 * Redirect on thx action
	 *
	 * @return void
	 */
	protected function redirectToTarget() {
		$target = NULL;

		// redirect from flexform
		if (!empty($this->settings['thx']['redirect'])) {
			$target = $this->settings['thx']['redirect'];
		}

		// redirect from TypoScript cObject
		if ($this->cObj->cObjGetSingle($this->conf['thx.']['overwrite.']['redirect'], $this->conf['thx.']['overwrite.']['redirect.'])) {
			$target = $this->cObj->cObjGetSingle(
				$this->conf['thx.']['overwrite.']['redirect'],
				$this->conf['thx.']['overwrite.']['redirect.']
			);
		}

		// if redirect target
		if ($target) {
			$this->uriBuilder->setTargetPageUid($target);
			$link = $this->uriBuilder->build();
			$this->redirectToUri($link);
		}
		return;
	}

	/**
	 * Save mail on submit
	 *
	 * @param array $field Field values
	 * @param int $form Form uid
	 * @return Tx_Powermail_Domain_Model_Mails Mail object
	 */
	protected function saveMail($field, $form) {
		// tx_powermail_domain_model_mails
		$marketingInfos = Tx_Powermail_Utility_Div::getMarketingInfos();
		$newMail = $this->objectManager->create('Tx_Powermail_Domain_Model_Mails');
		$newMail->setPid(Tx_Powermail_Utility_Div::getStoragePage($this->settings['main']['pid']));
		$newMail->setForm($form);
		$newMail->setSenderMail($this->div->getSenderMailFromArguments($field));
		$newMail->setSenderName($this->div->getSenderNameFromArguments($field));
		$newMail->setSubject($this->settings['receiver']['subject']);
		$newMail->setBody(t3lib_utility_Debug::viewArray($this->div->getVariablesWithLabels($field)));
		$newMail->setReceiverMail($this->settings['receiver']['email']);
		if (intval($GLOBALS['TSFE']->fe_user->user['uid']) > 0) {
			$newMail->setFeuser($GLOBALS['TSFE']->fe_user->user['uid']);
		}
		$newMail->setSpamFactor($GLOBALS['TSFE']->fe_user->getKey('ses', 'powermail_spamfactor'));
		$newMail->setTime((time() - Tx_Powermail_Utility_Div::getFormStartFromSession($form)));
		if (isset($this->settings['global']['disableIpLog']) && $this->settings['global']['disableIpLog'] == 0) {
			$newMail->setSenderIp(t3lib_div::getIndpEnv('REMOTE_ADDR'));
		}
		$newMail->setUserAgent(t3lib_div::getIndpEnv('HTTP_USER_AGENT'));
		$newMail->setMarketingSearchterm($marketingInfos['marketingSearchterm']);
		$newMail->setMarketingReferer($marketingInfos['marketingReferer']);
		$newMail->setMarketingPayedSearchResult($marketingInfos['marketingPayedSearchResult']);
		$newMail->setMarketingLanguage($marketingInfos['marketingLanguage']);
		$newMail->setMarketingBrowserLanguage($marketingInfos['marketingBrowserLanguage']);
		$newMail->setMarketingFunnel($marketingInfos['marketingFunnel']);
		if ($this->settings['main']['optin'] || $this->settings['db']['hidden']) {
			$newMail->setHidden(1);
		}
		$this->mailsRepository->add($newMail);
		$persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
		$persistenceManager->persistAll();

		// tx_powermail_domain_model_answers
		foreach ((array) $field as $uid => $value) { // one loop for every received field
			if (!is_numeric($uid)) {
				continue;
			}
			$newAnswer = $this->objectManager->create('Tx_Powermail_Domain_Model_Answers');
			$newAnswer->setPid(Tx_Powermail_Utility_Div::getStoragePage($this->settings['main']['pid']));
			$newAnswer->setValue($value);
			$newAnswer->setField($uid);
			$newAnswer->setMail($newMail->getUid());

			$this->answersRepository->add($newAnswer);
		}
		$persistenceManager->persistAll();

		return $newMail;
	}

	/**
	 * Confirm Double Optin
	 *
	 * @param int $mail Mail Uid
	 * @param string $hash Given Hash String
	 * @dontvalidate $mail
	 * @dontvalidate $hash
	 * return void
	 */
	public function optinConfirmAction($mail = null, $hash = null) {
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', array($mail, $hash, $this));
		$mail = $this->mailsRepository->findByUid($mail);

		if (
			!empty($hash) &&
			$mail instanceof Tx_Powermail_Domain_Model_Mails &&
			$hash == Tx_Powermail_Utility_Div::createOptinHash($mail->getUid() . $mail->getPid() . $mail->getForm()->getUid())
		) {
			$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'AfterSuccessfulConfirmation', array($mail, $hash, $this));
			// only if hidden = 0
			if ($mail->getHidden() == 1) {
				$mail->setHidden(0);

				$this->mailsRepository->update($mail);
				$persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
				$persistenceManager->persistAll();

				// call create action
				$fields = array();
				foreach ($mail->getAnswers() as $answer) {
					$fields[$answer->getField()] = $this->div->getDateFromTimestamp($answer->getValue(), $answer->getField());
				}
				$arguments = array(
					'field' => $fields,
					'form' => $mail->getForm()->getUid(),
					'mail' => $mail->getUid(),
					'__referrer' => array(
						'actionName' => 'optinConfirm'
					)
				);
				$_POST['tx_powermail_pi1']['__referrer']['actionName'] = 'optinConfirm'; // workarround to set the referrer and call it again in the validator
				$this->forward('create', null, null, $arguments);
			}
		}
	}

	/**
	 * Initializes this object
	 *
	 * @return void
	 */
	public function initializeAction() {
		$this->cObj = $this->configurationManager->getContentObject();
		$typoScriptSetup = $this->configurationManager->getConfiguration(
			Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
		);
		$this->conf = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];

		// merge typoscript to flexform (if flexform field also exists and is empty, take typoscript part)
		Tx_Powermail_Utility_Div::mergeTypoScript2FlexForm($this->settings);
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'Settings', array($this));

		// check if ts is included
		if (!isset($this->settings['staticTemplate'])) {
			if ($this->controllerContext === NULL) {
				$this->controllerContext = $this->buildControllerContext();
			}
			$this->flashMessageContainer->add(
				Tx_Extbase_Utility_Localization::translate('error_no_typoscript', 'powermail')
			);
		}

		// Debug Output
		if ($this->settings['debug']['settings']) {
			t3lib_utility_Debug::debug($this->settings, 'powermail debug: Show Settings');
		}
	}

	/**
	 * Forward to form action if wrong form in plugin variables
	 *
	 * @param int $form Form Uid
	 * @return void
	 */
	protected function ignoreWrongForm($form) {
		$pluginHasThisAssignedForms = t3lib_div::intExplode(',', $this->settings['main']['form']);
		if (!in_array($form, $pluginHasThisAssignedForms)) {
			$this->forward('form');
		}
	}

	/**
	 * Deactivate errormessages in flashmessages
	 *
	 * @return bool
	 */
	protected function getErrorFlashMessage() {
		return FALSE;
	}

	/**
	 * @param Tx_Powermail_Utility_Div $div
	 * @return void
	 */
	public function injectDiv(Tx_Powermail_Utility_Div $div) {
		$this->div = $div;
	}

	/**
	 * injectFormsRepository
	 *
	 * @param Tx_Powermail_Domain_Repository_FormsRepository $formsRepository
	 * @return void
	 */
	public function injectFormsRepository(Tx_Powermail_Domain_Repository_FormsRepository $formsRepository) {
		$this->formsRepository = $formsRepository;
	}

	/**
	 * injectMailsRepository
	 *
	 * @param Tx_Powermail_Domain_Repository_MailsRepository $mailsRepository
	 * @return void
	 */
	public function injectMailsRepository(Tx_Powermail_Domain_Repository_MailsRepository $mailsRepository) {
		$this->mailsRepository = $mailsRepository;
	}

	/**
	 * injectAnswersRepository
	 *
	 * @param Tx_Powermail_Domain_Repository_AnswersRepository $answersRepository
	 * @return void
	 */
	public function injectAnswersRepository(Tx_Powermail_Domain_Repository_AnswersRepository $answersRepository) {
		$this->answersRepository = $answersRepository;
	}

	/**
	 * @param Tx_Extbase_SignalSlot_Dispatcher $signalSlotDispatcher
	 */
	public function injectSignalSlotDispatcher(Tx_Extbase_SignalSlot_Dispatcher $signalSlotDispatcher) {
		$this->signalSlotDispatcher = $signalSlotDispatcher;
	}

}