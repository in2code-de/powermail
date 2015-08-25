<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\SessionUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Main Send Mail Class
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class SendMailService {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * mailRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\MailRepository
	 * @inject
	 */
	protected $mailRepository;

	/**
	 * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher;

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObject;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @var array
	 */
	protected $overwriteConfiguration;

	/**
	 * @var Mail
	 */
	protected $mail;

	/**
	 * @var string
	 */
	protected $type = 'receiver';

	/**
	 * This is the main-function for sending Mails
	 *
	 * @param array $email Array with all needed mail information
	 * 		$email['receiverName'] = 'Name';
	 * 		$email['receiverEmail'] = 'receiver@mail.com';
	 * 		$email['senderName'] = 'Name';
	 * 		$email['senderEmail'] = 'sender@mail.com';
	 * 		$email['subject'] = 'Subject line';
	 * 		$email['template'] = 'PathToTemplate/';
	 * 		$email['rteBody'] = 'This is the <b>content</b> of the RTE';
	 * 		$email['format'] = 'both'; // or plain or html
	 * @param Mail &$mail
	 * @param array $settings TypoScript Settings
	 * @param string $type Email to "sender" or "receiver"
	 * @return bool Mail successfully sent
	 */
	public function sendEmailPreflight(array $email, Mail &$mail, $settings, $type = 'receiver') {
		$this->mail = &$mail;
		$this->settings = $settings;
		$this->configuration = $this->getConfigurationFromSettings($settings);
		$this->overwriteConfiguration = $this->configuration[$type . '.']['overwrite.'];
		$this->contentObject = $this->configurationManager->getContentObject();
		$this->contentObject->start($this->mailRepository->getVariablesWithMarkersFromMail($mail));
		$this->type = $type;
		$this->parseVariables($email, $mail);
		if ($settings['debug']['mail']) {
			GeneralUtility::devLog('Mail properties', 'powermail', 0, $email);
		}
		if (!GeneralUtility::validEmail($email['receiverEmail']) || !GeneralUtility::validEmail($email['senderEmail'])) {
			return FALSE;
		}
		if (empty($email['subject'])) {
			// don't want an error flashmessage
			return TRUE;
		}
		return $this->sendTemplateEmail($email);
	}

	/**
	 * Send the mail
	 *
	 * @param array $email Array with all needed mail information
	 * @return bool Mail successfully sent
	 */
	protected function sendTemplateEmail(array $email) {
		/** @var MailMessage $message */
		$message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
		TypoScriptUtility::overwriteValueFromTypoScript($email['subject'], $this->overwriteConfiguration, 'subject');
		$message
			->setTo(array($email['receiverEmail'] => $email['receiverName']))
			->setFrom(array($email['senderEmail'] => $email['senderName']))
			->setSubject($email['subject'])
			->setCharset($this->getFrontendCharset());
		$message = $this->addCc($message);
		$message = $this->addBcc($message);
		$message = $this->addReturnPath($message);
		$message = $this->addReplyAddresses($message);
		$message = $this->addPriority($message);
		$message = $this->addAttachmentsFromUploads($message);
		$message = $this->addAttachmentsFromTypoScript($message);
		$message = $this->addHtmlBody($message, $email);
		$message = $this->addPlainBody($message, $email);

		$this->signalSlotDispatcher->dispatch(
			__CLASS__,
			__FUNCTION__ . 'BeforeSend',
			array($message, $email, $this->mail, $this->settings, $this->type)
		);

		$message->send();
		$this->updateMail($email);
		return $message->isSent();
	}

	/**
	 * Add CC receivers
	 *
	 * @param MailMessage $message
	 * @return MailMessage
	 */
	protected function addCc(MailMessage $message) {
		$ccValue = $this->contentObject->cObjGetSingle($this->overwriteConfiguration['cc'], $this->overwriteConfiguration['cc.']);
		if (!empty($ccValue)) {
			$message->setCc(GeneralUtility::trimExplode(',', $ccValue, TRUE));
		}
		return $message;
	}

	/**
	 * Add BCC receivers
	 *
	 * @param MailMessage $message
	 * @return MailMessage
	 */
	protected function addBcc(MailMessage $message) {
		$bccValue = $this->contentObject->cObjGetSingle($this->overwriteConfiguration['bcc'], $this->overwriteConfiguration['bcc.']);
		if (!empty($bccValue)) {
			$message->setBcc(GeneralUtility::trimExplode(',', $bccValue, TRUE));
		}
		return $message;
	}

	/**
	 * Add return path
	 *
	 * @param MailMessage $message
	 * @return MailMessage
	 */
	protected function addReturnPath(MailMessage $message) {
		$returnPathValue = $this->contentObject->cObjGetSingle(
			$this->overwriteConfiguration['returnPath'],
			$this->overwriteConfiguration['returnPath.']
		);
		if (!empty($returnPathValue)) {
			$message->setReturnPath($returnPathValue);
		}
		return $message;
	}

	/**
	 * Add reply addresses if replyToEmail and replyToName isset
	 *
	 * @param MailMessage $message
	 * @return MailMessage
	 */
	protected function addReplyAddresses(MailMessage $message) {
		$replyToEmail = $this->contentObject->cObjGetSingle(
			$this->overwriteConfiguration['replyToEmail'],
			$this->overwriteConfiguration['replyToEmail.']
		);
		$replyToName = $this->contentObject->cObjGetSingle(
			$this->overwriteConfiguration['replyToName'],
			$this->overwriteConfiguration['replyToName.']
		);
		if (!empty($replyToEmail) && !empty($replyToName)) {
			$message->setReplyTo(
				array(
					$replyToEmail => $replyToName
				)
			);
		}
		return $message;
	}

	/**
	 * Add mail priority
	 *
	 * @param MailMessage $message
	 * @return MailMessage
	 */
	protected function addPriority(MailMessage $message) {
		$priorityValue = (int) $this->settings[$this->type]['overwrite']['priority'];
		if ($priorityValue > 0) {
			$message->setPriority($priorityValue);
		}
		return $message;
	}

	/**
	 * Add attachments from upload fields
	 *
	 * @param MailMessage $message
	 * @return MailMessage
	 */
	protected function addAttachmentsFromUploads(MailMessage $message) {
		if (empty($this->settings[$this->type]['attachment'])) {
			return $message;
		}
		/** @var Answer $answer */
		foreach ($this->mail->getAnswers() as $answer) {
			$values = $answer->getValue();
			if ($answer->getValueType() === 3 && is_array($values) && !empty($values)) {
				foreach ($values as $value) {
					$file = $this->settings['misc']['file']['folder'] . $value;
					if (file_exists(GeneralUtility::getFileAbsFileName($file))) {
						$message->attach(\Swift_Attachment::fromPath($file));
					} else {
						GeneralUtility::devLog('Error: File to attach does not exist', 'powermail', 2, $file);
					}
				}
			}
		}
		return $message;
	}

	/**
	 * Add attachments from TypoScript definition
	 *
	 * @param MailMessage $message
	 * @return MailMessage
	 */
	protected function addAttachmentsFromTypoScript(MailMessage $message) {
		$filesValue = $this->contentObject->cObjGetSingle(
			$this->configuration[$this->type . '.']['addAttachment'],
			$this->configuration[$this->type . '.']['addAttachment.']
		);
		if (!empty($filesValue)) {
			$files = GeneralUtility::trimExplode(',', $filesValue, TRUE);
			foreach ($files as $file) {
				if (file_exists(GeneralUtility::getFileAbsFileName($file))) {
					$message->attach(\Swift_Attachment::fromPath($file));
				} else {
					GeneralUtility::devLog('Error: File to attach does not exist', 'powermail', 2, $file);
				}
			}
		}
		return $message;
	}

	/**
	 * Add mail body html
	 *
	 * @param MailMessage $message
	 * @param array $email
	 * @return MailMessage
	 */
	protected function addHtmlBody(MailMessage $message, array $email) {
		if ($email['format'] !== 'plain') {
			$message->setBody($this->createEmailBody($email), 'text/html');
		}
		return $message;
	}

	/**
	 * Add mail body plain
	 *
	 * @param MailMessage $message
	 * @param array $email
	 * @return MailMessage
	 */
	protected function addPlainBody(MailMessage $message, array $email) {
		if ($email['format'] !== 'html') {
			$message->addPart($this->makePlain($this->createEmailBody($email)), 'text/plain');
		}
		return $message;
	}

	/**
	 * Create Email Body
	 *
	 * @param array $email Array with all needed mail information
	 * @return bool
	 */
	protected function createEmailBody($email) {
		$standaloneView = TemplateUtility::getDefaultStandAloneView();
		$standaloneView->getRequest()->setControllerName('Form');
		$standaloneView->setTemplatePathAndFilename(
			TemplateUtility::getTemplatePath($email['template'] . '.html')
		);
		$standaloneView->setLayoutRootPaths(TemplateUtility::getTemplateFolders('layout'));
		$standaloneView->setPartialRootPaths(TemplateUtility::getTemplateFolders('partial'));

		// variables
		$variablesWithMarkers = $this->mailRepository->getVariablesWithMarkersFromMail($this->mail);
		$standaloneView->assignMultiple($variablesWithMarkers);
		$standaloneView->assignMultiple($this->mailRepository->getLabelsWithMarkersFromMail($this->mail));
		$standaloneView->assignMultiple(
			array(
				'variablesWithMarkers' => ArrayUtility::htmlspecialcharsOnArray($variablesWithMarkers),
				'powermail_all' => TemplateUtility::powermailAll($this->mail, 'mail', $this->settings, $this->type),
				'powermail_rte' => $email['rteBody'],
				'marketingInfos' => SessionUtility::getMarketingInfos(),
				'mail' => $this->mail,
				'email' => $email,
				'settings' => $this->settings
			)
		);
		if (!empty($email['variables'])) {
			$standaloneView->assignMultiple($email['variables']);
		}

		$this->signalSlotDispatcher->dispatch(
			__CLASS__,
			__FUNCTION__ . 'BeforeRender',
			array($standaloneView, $email, $this->mail, $this->settings)
		);

		$body = $standaloneView->render();
		$this->mail->setBody($body);
		return $body;
	}

	/**
	 * Function makePlain() removes html tags and add linebreaks
	 * 		Easy generate a plain email bodytext from a html bodytext
	 *
	 * @param string $content HTML Mail bodytext
	 * @return string $content
	 */
	protected function makePlain($content) {
		$tags2LineBreaks = array (
			'</p>',
			'</tr>',
			'<ul>',
			'</li>',
			'</h1>',
			'</h2>',
			'</h3>',
			'</h4>',
			'</h5>',
			'</h6>',
			'</div>',
			'</legend>',
			'</fieldset>',
			'</dd>',
			'</dt>'
		);

		// 1. remove linebreaks, tabs
		$content = trim(str_replace(array("\n", "\r", "\t"), '', $content));
		// 2. add linebreaks on some parts (</p> => </p><br />)
		$content = str_replace($tags2LineBreaks, '</p><br />', $content);
		// 3. insert space for table cells
		$content = str_replace(array('</td>', '</th>'), '</td> ', $content);
		// 4. remove all tags (<b>bla</b><br /> => bla<br />)
		$content = strip_tags($content, '<br><address>');
		// 5. <br /> to \n
		$content = $this->br2nl($content);

		return trim($content);
	}

	/**
	 * Function br2nl is the opposite of nl2br
	 *
	 * @param string $content Anystring
	 * @return string $content Manipulated string
	 */
	protected function br2nl($content) {
		$array = array(
			'<br >',
			'<br>',
			'<br/>',
			'<br />'
		);
		$content = str_replace($array, "\n", $content);

		return $content;
	}

	/**
	 * Update mail record with parsed fields
	 *
	 * @param array $email
	 */
	protected function updateMail(array $email) {
		if ($this->type === 'receiver' && $email['variables']['hash'] === NULL) {
			$this->mail->setSenderMail($email['senderEmail']);
			$this->mail->setSenderName($email['senderName']);
			$this->mail->setSubject($email['subject']);
		}
	}

	/**
	 * @param array $settings
	 * @return array
	 */
	protected function getConfigurationFromSettings(array $settings) {
		/** @var TypoScriptService $typoScriptService */
		$typoScriptService = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
		return $typoScriptService->convertPlainArrayToTypoScriptArray($settings);
	}

	/**
	 * Parsing variables with fluid engine to allow viewhelpers in flexform
	 *
	 * @param array $email
	 * @param Mail $mail
	 * @return void
	 */
	protected function parseVariables(array &$email, Mail &$mail) {
		$parse = array(
			'receiverName',
			'receiverEmail',
			'senderName',
			'senderEmail',
			'subject'
		);
		foreach ($parse as $value) {
			$email[$value] =
				TemplateUtility::fluidParseString($email[$value], $this->mailRepository->getVariablesWithMarkersFromMail($mail));
		}
	}

	/**
	 * @return string
	 */
	protected function getFrontendCharset() {
		return $GLOBALS['TSFE']->metaCharset;
	}
}