<?php
namespace In2code\Powermail\Command;

use \In2code\Powermail\Utility\BasicFileFunctions,
	\TYPO3\CMS\Core\Utility\GeneralUtility;

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
class TaskCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController {

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
	 * Remove unused uploaded Files
	 * 		with a scheduler task
	 *
	 * @param string $uploadPath Define the upload Path
	 * @return void
	 */
	public function cleanUnusedUploadsCommand($uploadPath = 'uploads/tx_powermail/') {
		/**
		 * Open on Command Line with
		 * php cli_dispatch.phpsh extbase task:cleanunuseduploads
		 * Needs BE-User _cli_lowlevel
		 */
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
}