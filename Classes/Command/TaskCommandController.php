<?php
namespace In2code\Powermail\Command;

use In2code\Powermail\Utility\BasicFileFunctions;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

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
 * Controller for powermail tasks
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class TaskCommandController extends CommandController {

	/**
	 * mailRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\MailRepository
	 * @inject
	 */
	protected $mailRepository;

	/**
	 * answerRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\AnswerRepository
	 * @inject
	 */
	protected $answerRepository;

	/**
	 * delete Files which are older than this seconds
	 * 
	 * @var int
	 */
	protected $delta = 3600;

	/**
	 * Export of mails as email attachment
	 *
	 * 		This task can send a mail export with an attachment
	 * 		(XLS or CSV) to a receiver or a group of receivers
	 *
	 * @param string $receiverEmails comma separated email addresses for export
	 * @param string $senderEmail sender email address
	 * @param string $subject Mail subject
	 * @param int $pageUid Page Id with existing mails
	 * @param string $domain Domainname for linkgeneration
	 * @param int $period Select mails that are not older than this seconds
	 * @param boolean $attachment Add export file as attachment to mail
	 * @param string $fieldList Define needed fields with a commasepareted uid list (empty = all default fields)
	 * @param string $format Fileformat can be 'xls' or 'csv'
	 * @param string $storageFolder path where to save export file
	 * @return bool
	 */
	public function exportCommand(
		$receiverEmails,
		$senderEmail = 'sender@domain.org',
		$subject = 'New mail export',
		$pageUid = 0,
		$domain = 'http://www.domain.org/',
		$period = 2592000,
		$attachment = TRUE,
		$fieldList = '',
		$format = 'xls',
		$storageFolder = 'typo3temp/tx_powermail/'
	) {
		/** @var \In2code\Powermail\Utility\Export $export */
		$export = $this->objectManager->get(
			'In2code\Powermail\Utility\Export',
			$this->mailRepository->findAllInPid($pageUid, array(), $this->getFilterVariables($period)),
			$format,
			array('domain' => $domain)
		);
		$export
			->setReceiverEmails($receiverEmails)
			->setSenderEmails($senderEmail)
			->setSubject($subject)
			->setFieldList($fieldList)
			->setAddAttachment($attachment)
			->setStorageFolder($storageFolder);
		return $export->send();
	}

	/**
	 * Remove unused uploaded Files with a scheduler task
	 *
	 * 		This task can clean up unused uploaded files
	 * 		with powermail from your server
	 *
	 * @param string $uploadPath Define the upload Path
	 * @return void
	 */
	public function cleanUnusedUploadsCommand($uploadPath = 'uploads/tx_powermail/') {
		$usedUploads = $this->getUsedUploads();
		$allUploads = BasicFileFunctions::getFilesFromRelativePath($uploadPath);
		$removeCounter = 0;
		foreach ($allUploads as $upload) {
			if (!in_array($upload, $usedUploads)) {
				$absoluteFilePath = GeneralUtility::getFileAbsFileName($uploadPath . $upload);
				if (filemtime($absoluteFilePath) < (time() - $this->delta)) {
					unlink($absoluteFilePath);
					$removeCounter++;
				}
			}
		}
		$this->outputLine('Overall Files: ' . count($allUploads));
		$this->outputLine('Removed Files: ' . $removeCounter);
	}

	/**
	 * Remove all export files in typo3temp/tx_powermail/
	 *
	 * 		This task will clean up all (!) files which
	 * 		are located in typo3temp/tx_powermail/
	 * 		e.g.: old captcha images and old export files
	 * 		(from export task - if stored in typo3temp folder)
	 *
	 * @return void
	 */
	public function cleanExportFilesCommand() {
		$files = GeneralUtility::getFilesInDir(
			GeneralUtility::getFileAbsFileName('typo3temp/tx_powermail/'),
			'',
			TRUE
		);
		foreach ($files as $file) {
			unlink($file);
		}
	}

	/**
	 * Get used uploads
	 *
	 * @return array
	 */
	protected function getUsedUploads() {
		$answers = $this->answerRepository->findByAnyUpload();
		$usedUploads = array();
		foreach ($answers as $answer) {
			foreach ((array) $answer->getValue() as $singleUpload) {
				$usedUploads[] = $singleUpload;
			}
		}
		return $usedUploads;
	}

	/**
	 * Create a filter array from given period
	 *
	 * @param int $period
	 * @return array
	 */
	protected function getFilterVariables($period) {
		$variables = array('filter' => array());
		if ($period > 0) {
			$variables = array(
				'filter' => array(
					'start' => strftime('%Y-%m-%d', (time() - $period)),
					'stop' => 'now'
				)
			);
		}
		return $variables;
	}
}