<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\TypoScriptService;
use In2code\Powermail\Domain\Model\Mail;

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
class SendMail {

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
	 * @var \In2code\Powermail\Utility\Div
	 * @inject
	 */
	protected $div;

	/**
	 * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher;

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
	 * @param \In2code\Powermail\Domain\Model\Mail &$mail
	 * @param array $settings TypoScript Settings
	 * @param string $type Email to "sender" or "receiver"
	 * @return bool Mail successfully sent
	 */
	public function sendTemplateEmail(array $email, Mail &$mail, $settings, $type = 'receiver') {
		/** @var TypoScriptService $typoScriptService */
		$typoScriptService = $this->objectManager->get('TYPO3\CMS\Extbase\Service\TypoScriptService');
		$conf = $typoScriptService->convertPlainArrayToTypoScriptArray($settings);
		$cObj = $this->configurationManager->getContentObject();
		$cObj->start($this->div->getVariablesWithMarkersFromMail($mail));

		// parsing variables with fluid engine to allow viewhelpers in flexform
		$parse = array(
			'receiverName',
			'receiverEmail',
			'senderName',
			'senderEmail',
			'subject'
		);
		foreach ($parse as $value) {
			$email[$value] = $this->div->fluidParseString($email[$value], $this->div->getVariablesWithMarkersFromMail($mail));
		}

		// Debug Output
		if ($settings['debug']['mail']) {
			GeneralUtility::devLog(
				'Mail properties',
				'powermail',
				0,
				$email
			);
		}

		// stop mail process if receiver or sender email is not valid
		if (!GeneralUtility::validEmail($email['receiverEmail']) || !GeneralUtility::validEmail($email['senderEmail'])) {
			return FALSE;
		}

		// stop mail process if subject is empty
		if (empty($email['subject'])) {
			// don't want an error flashmessage
			return TRUE;
		}

		/** @var \TYPO3\CMS\Core\Mail\MailMessage $message */
		$message = GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');
		$this->div->overwriteValueFromTypoScript($email['subject'], $conf[$type . '.']['overwrite.'], 'subject');
		$message
			->setTo(array($email['receiverEmail'] => $email['receiverName']))
			->setFrom(array($email['senderEmail'] => $email['senderName']))
			->setSubject($email['subject'])
			->setCharset($GLOBALS['TSFE']->metaCharset);

		// add cc receivers
		if ($cObj->cObjGetSingle($conf[$type . '.']['overwrite.']['cc'], $conf[$type . '.']['overwrite.']['cc.'])) {
			$ccArray = GeneralUtility::trimExplode(
				',',
				$cObj->cObjGetSingle($conf[$type . '.']['overwrite.']['cc'],
					$conf[$type . '.']['overwrite.']['cc.']),
				TRUE
			);
			$message->setCc($ccArray);
		}

		// add bcc receivers
		if ($cObj->cObjGetSingle($conf[$type . '.']['overwrite.']['bcc'], $conf[$type . '.']['overwrite.']['bcc.'])) {
			$bccArray = GeneralUtility::trimExplode(
				',',
				$cObj->cObjGetSingle($conf[$type . '.']['overwrite.']['bcc'],
					$conf[$type . '.']['overwrite.']['bcc.']),
				TRUE
			);
			$message->setBcc($bccArray);
		}

		// add Return Path
		if ($cObj->cObjGetSingle($conf[$type . '.']['overwrite.']['returnPath'], $conf[$type . '.']['overwrite.']['returnPath.'])) {
			$message->setReturnPath(
				$cObj->cObjGetSingle($conf[$type . '.']['overwrite.']['returnPath'],
					$conf[$type . '.']['overwrite.']['returnPath.'])
			);
		}

		// add Reply Addresses
		if (
			$cObj->cObjGetSingle($conf[$type . '.']['overwrite.']['replyToEmail'], $conf[$type . '.']['overwrite.']['replyToEmail.'])
			&&
			$cObj->cObjGetSingle($conf[$type . '.']['overwrite.']['replyToName'], $conf[$type . '.']['overwrite.']['replyToName.'])
		) {
			$replyArray = array(
				$cObj->cObjGetSingle($conf[$type . '.']['overwrite.']['replyToEmail'], $conf[$type . '.']['overwrite.']['replyToEmail.']) =>
					$cObj->cObjGetSingle($conf[$type . '.']['overwrite.']['replyToName'], $conf[$type . '.']['overwrite.']['replyToName.'])
			);
			$message->setReplyTo($replyArray);
		}

		// add priority
		if ($settings[$type]['overwrite']['priority']) {
			$message->setPriority(intval($settings[$type]['overwrite']['priority']));
		}

		// add attachments from upload fields
		if ($settings[$type]['attachment']) {
			/** @var \In2code\Powermail\Domain\Model\Answer $answer */
			foreach ($mail->getAnswers() as $answer) {
				$values = $answer->getValue();
				if ($answer->getValueType() === 3 && is_array($values) && !empty($values)) {
					foreach ($values as $value) {
						$file = $settings['misc']['file']['folder'] . $value;
						if (file_exists(GeneralUtility::getFileAbsFileName($file))) {
							$message->attach(\Swift_Attachment::fromPath($file));
						} else {
							GeneralUtility::devLog('Error: File to attach does not exist', 'powermail', 2, $file);
						}
					}
				}
			}
		}

		// add attachments from TypoScript
		if ($cObj->cObjGetSingle($conf[$type . '.']['addAttachment'], $conf[$type . '.']['addAttachment.'])) {
			$files = GeneralUtility::trimExplode(
				',',
				$cObj->cObjGetSingle($conf[$type . '.']['addAttachment'], $conf[$type . '.']['addAttachment.']),
				TRUE
			);
			foreach ($files as $file) {
				if (file_exists(GeneralUtility::getFileAbsFileName($file))) {
					$message->attach(\Swift_Attachment::fromPath($file));
				} else {
					GeneralUtility::devLog('Error: File to attach does not exist', 'powermail', 2, $file);
				}
			}
		}
		if ($email['format'] != 'plain') {
			$message->setBody(
				$this->createEmailBody($email, $mail, $settings, $type),
				'text/html'
			);
		}
		if ($email['format'] != 'html') {
			$message->addPart(
				$this->makePlain($this->createEmailBody($email, $mail, $settings, $type)),
				'text/plain'
			);
		}

		$this->signalSlotDispatcher->dispatch(
			__CLASS__,
			__FUNCTION__ . 'BeforeSend',
			array($message, $email, $mail, $settings, $type)
		);

		$message->send();

		// update mail (with parsed fields)
		if ($type === 'receiver' && $email['variables']['hash'] === NULL) {
			$mail->setSenderMail($email['senderEmail']);
			$mail->setSenderName($email['senderName']);
			$mail->setSubject($email['subject']);
		}

		return $message->isSent();
	}

	/**
	 * Create Email Body
	 *
	 * @param array $email Array with all needed mail information
	 * @param \In2code\Powermail\Domain\Model\Mail &$mail
	 * @param array $settings TypoScript Settings
	 * @param string $type Mail type
	 * @return bool
	 */
	protected function createEmailBody($email, Mail &$mail, $settings, $type) {
		/** @var StandaloneViewMultiplePaths $emailBodyObject */
		$emailBodyObject = $this->objectManager->get('In2code\\Powermail\\Utility\\StandaloneViewMultiplePaths');
		$emailBodyObject->getRequest()->setControllerExtensionName('Powermail');
		$emailBodyObject->getRequest()->setPluginName('Pi1');
		$emailBodyObject->getRequest()->setControllerName('Form');
		$emailBodyObject->setFormat('html');
		$emailBodyObject->setTemplatePathAndFilename(
			$this->div->getTemplatePath($email['template'] . '.html')
		);
		$emailBodyObject->setLayoutRootPaths($this->div->getTemplateFolders('layout'));
		$emailBodyObject->setPartialRootPaths($this->div->getTemplateFolders('partial'));

		// variables
		$variablesWithMarkers = $this->div->getVariablesWithMarkersFromMail($mail);
		$emailBodyObject->assignMultiple($variablesWithMarkers);
		$emailBodyObject->assignMultiple($this->div->getLabelsWithMarkersFromMail($mail));
		$emailBodyObject->assignMultiple(
			array(
				'variablesWithMarkers' => $this->div->htmlspecialcharsOnArray($variablesWithMarkers),
				'powermail_all' => $this->div->powermailAll($mail, 'mail', $settings, $type),
				'powermail_rte' => $email['rteBody'],
				'marketingInfos' => Div::getMarketingInfos(),
				'mail' => $mail,
				'email' => $email,
				'settings' => $settings
			)
		);
		if (!empty($email['variables'])) {
			$emailBodyObject->assignMultiple($email['variables']);
		}

		$this->signalSlotDispatcher->dispatch(
			__CLASS__,
			__FUNCTION__ . 'BeforeRender',
			array($emailBodyObject, $email, $mail, $settings)
		);

		$body = $emailBodyObject->render();
		$mail->setBody($body);
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
		$rewriteTagsWithLineBreaks = array (
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
		$content = str_replace($rewriteTagsWithLineBreaks, '</p><br />', $content);
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
}