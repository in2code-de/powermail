<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Fluid\View\StandaloneView;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Model\Field;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class for generating and sending export files (xls or csv)
 * with scheduler
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class Export {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager = NULL;

	/**
	 * Contains mails for export
	 *
	 * @var null|QueryResult
	 */
	protected $mails = NULL;

	/**
	 * Receiver email addresses
	 *
	 * @var array
	 */
	protected $receiverEmails = array();

	/**
	 * Sender email addresses
	 *
	 * @var array
	 */
	protected $senderEmails = array(
		'powermail@domain.org'
	);

	/**
	 * Mail subject
	 *
	 * @var string
	 */
	protected $subject = '';

	/**
	 * Export format can be 'xls' or 'csv'
	 *
	 * @var string
	 */
	protected $format = 'xls';

	/**
	 * Fields to export
	 * 	Can be empty for all fields
	 * 	can contain:
	 * 		field uids
	 * 		"crdate"
	 * 		"sender_name"
	 * 		"sender_mail"
	 * 		"receiver_mail"
	 * 		"subject"
	 * 		"marketing_referer_domain"
	 * 		"marketing_referer"
	 * 		"marketing_frontend_language"
	 * 		"marketing_browser_language"
	 * 		"marketing_country"
	 * 		"marketing_mobile_device"
	 * 		"marketing_page_funnel"
	 * 		"user_agent"
	 * 		"time"
	 * 		"sender_ip"
	 * 		"uid"
	 * 		"feuser"
	 *
	 * @var array
	 */
	protected $fieldList = array();

	/**
	 * @var string
	 */
	protected $fileName = '';

	/**
	 * Constructor
	 *
	 * @param QueryResult $mails Given mails for export
	 * @param string $format can be 'xls' or 'csv'
	 */
	public function __construct(QueryResult $mails, $format = 'xls') {
		$this->setMails($mails);
		$this->setFormat($format);
		$this->setFieldList(
			$this->getDefaultFieldListFromFirstMail($mails)
		);
		$this->createFileName();
	}

	/**
	 * send mail
	 *
	 * @return mixed mail send status
	 */
	public function send() {
		$result = $this->createExportFile();
		if ($result !== NULL) {
			return $result;
		}
		return $this->sendEmail();
	}

	/**
	 * @return bool
	 */
	protected function sendEmail() {
		/** @var \TYPO3\CMS\Core\Mail\MailMessage $email */
		$email = GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');
		$email->setTo($this->getReceiverEmails());
		$email->setFrom($this->getSenderEmails());
		$email->setSubject($this->getSubject());
		$email->setBody($this->createMailBody());
		$email->setFormat('html');
		$email->attach(\Swift_Attachment::fromPath($this->getPathAndFileName()));
		$email->send();
		return $email->isSent();
	}

	/**
	 * @return string
	 */
	protected function createMailBody() {
		$body = 'New Mail Export ';
		$body .= '(' . Div::conditionalVariable(GeneralUtility::getIndpEnv('HTTP_HOST'), 'Powermail') . ')';
		$body .= "\n";
		$body .= 'Time: ' . strftime('%d.%m.%Y %H:%M:%S');
		return $body;
	}

	/**
	 * Create an export file
	 *
	 * @return string Returns NULL if success otherwise error string
	 */
	protected function createExportFile() {
		return GeneralUtility::writeFileToTypo3tempDir($this->getPathAndFileName(), $this->getFileContent());
	}

	/**
	 * @return string
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidActionNameException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidControllerNameException
	 */
	protected function getFileContent() {
		/** @var StandaloneView $standAloneView */
		$standAloneView = $this->objectManager->get('TYPO3\CMS\Fluid\View\StandaloneView');
		$standAloneView->getRequest()->setControllerExtensionName('Powermail');
		$standAloneView->getRequest()->setPluginName('Pi1');
		$standAloneView->setFormat('html');
		$rootPath = GeneralUtility::getFileAbsFileName('EXT:powermail/Resources/Private/');
		$standAloneView->setTemplatePathAndFilename($rootPath . $this->getRelativeTemplatePathAndFileName());
		$standAloneView->setLayoutRootPaths(array($rootPath . 'Layouts'));
		$standAloneView->setPartialRootPaths(array($rootPath . 'Partials'));
		$standAloneView->assignMultiple(
			array(
				'mails' => $this->getMails(),
				'fieldUids' => $this->getFieldList()
			)
		);
		return $standAloneView->render();
	}

	/**
	 * @return string
	 */
	protected function getRelativeTemplatePathAndFileName() {
		return 'Templates/Module/Export' . ucfirst($this->getFormat()) . '.html';
	}

	/**
	 * @param QueryResult $mails
	 * @return array
	 */
	protected function getDefaultFieldListFromFirstMail(QueryResult $mails) {
		$fieldList = array();
		/** @var Mail $mail */
		$mail = $mails->getFirst();
		if ($mail !== NULL) {
			foreach ($mail->getForm()->getPages() as $page) {
				/** @var Field $field */
				foreach ($page->getFields() as $field) {
					if ($field->isBasicFieldType()) {
						$fieldList[] = $field->getUid();
					}
				}
			}
		}
		return $fieldList;
	}

	/**
	 * @return QueryResult
	 */
	public function getMails() {
		return $this->mails;
	}

	/**
	 * @param QueryResult $mails
	 * @return void
	 */
	public function setMails($mails) {
		$this->mails = $mails;
	}

	/**
	 * @return string
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * @param string $format
	 * @return void
	 */
	public function setFormat($format) {
		$this->format = $format;
	}

	/**
	 * @return array
	 */
	public function getFieldList() {
		return $this->fieldList;
	}

	/**
	 * @param string|array $fieldList
	 * @return void
	 */
	public function setFieldList($fieldList) {
		if (!empty($fieldList)) {
			if (is_string($fieldList)) {
				$fieldList = GeneralUtility::trimExplode(',', $fieldList, TRUE);
			}
			$this->fieldList = $fieldList;
		}
	}

	/**
	 * Get an array prepared for mail function
	 * 		array(
	 * 			'mail1@mail.org' => '',
	 * 			'mail2@mail.org' => ''
	 * 		)
	 *
	 * @return array
	 */
	public function getReceiverEmails() {
		$mailArray = array();
		foreach ($this->receiverEmails as $email) {
			$mailArray[$email] = '';
		}
		return $mailArray;
	}

	/**
	 * @param string|array $emails
	 * @return void
	 */
	public function setReceiverEmails($emails) {
		if (is_string($emails)) {
			$emails = GeneralUtility::trimExplode(',', $emails, TRUE);
		}
		$this->receiverEmails = $emails;
	}

	/**
	 * Get an array prepared for mail function
	 * 		array(
	 * 			'mail1@mail.org' => 'Sender',
	 * 			'mail2@mail.org' => 'Sender'
	 * 		)
	 *
	 * @return array
	 */
	public function getSenderEmails() {
		$mailArray = array();
		foreach ($this->senderEmails as $email) {
			$mailArray[$email] = 'Sender';
		}
		return $mailArray;
	}

	/**
	 * @param array $senderEmails
	 * @return void
	 */
	public function setSenderEmails($senderEmails) {
		if (is_string($senderEmails)) {
			$senderEmails = GeneralUtility::trimExplode(',', $senderEmails, TRUE);
		}
		$this->senderEmails = $senderEmails;
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @param string $subject
	 * @return void
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 * Create a random filename
	 *
	 * @return void
	 */
	protected function createFileName() {
		/**
		 * Note:
		 * \TYPO3\CMS\Core\Utility\GeneralUtility::writeFileToTypo3tempDir
		 * allows only filenames which are max 59 characters long
		 */
		$fileName = Div::createRandomString(55);
		$fileName .= '.';
		$fileName .= $this->getFormat();
		$this->fileName = $fileName;
	}

	/**
	 * @return string
	 */
	public function getFileName() {
		return $this->fileName;
	}

	/**
	 * Get absolute path and filename
	 *
	 * @return string
	 */
	protected function getPathAndFileName() {
		return GeneralUtility::getFileAbsFileName('typo3temp/tx_powermail/' . $this->getFileName());
	}
}