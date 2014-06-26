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
 * Class for uploading files and check if they are valid
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class Tx_Powermail_Domain_Validator_UploadValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * fieldsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FieldsRepository
	 */
	protected $fieldsRepository;

	/**
	 * @var Tx_Extbase_SignalSlot_Dispatcher
	 */
	protected $signalSlotDispatcher;

	/**
	 * BasicFileFunctions
	 */
	public $basicFileFunctions;

	/**
	 * TS
	 */
	public $settings;

	/**
	 * configurationManager
	 */
	public $configurationManager;

	/**
	 * Return variable
	 *
	 * @var bool
	 */
	protected $isValid = TRUE;

	/**
	 * Validation of given Params
	 *
	 * @param array $field
	 * @return bool
	 */
	public function isValid($field) {
		if (isset($_FILES['tx_powermail_pi1']['name']['field'])) {
			// session stuff
			$uploadSession = array();
			Tx_Powermail_Utility_Div::setSessionValue('upload', array(), TRUE);

			foreach ($_FILES['tx_powermail_pi1']['name']['field'] as $uid => $filename) {

				$field = $this->fieldsRepository->findByUid($uid);
				if ($field->getType() !== 'file') {
					continue;
				}

				// if no file given
				if (empty($filename)) {
					continue;
				}

				// Check extension
				if (!$this->checkExtension($filename, $uid)) {
					continue;
				}

				// Check filesize
				if (!$this->checkFilesize($uid)) {
					continue;
				}

				// create new filename with absolute path
				$newFile = $this->basicFileFunctions->getUniqueName(
					$filename, t3lib_div::getFileAbsFileName($this->settings['misc.']['file.']['folder'])
				);
				$uploadSession[] = $newFile;
				if (!t3lib_div::upload_copy_move($_FILES['tx_powermail_pi1']['tmp_name']['field'][$uid], $newFile)) {
					$this->addError('upload_error', $uid);
					$this->isValid = FALSE;
				}
			}

			// save uploaded filenames to session (to attach it later)
			Tx_Powermail_Utility_Div::setSessionValue('upload', $uploadSession, TRUE);
		}

		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'UploadValidation', array($field, $this));

		return $this->isValid;
	}

	/**
	 * Check filesize of given file
	 *
	 * @param int $uid Field uid
	 * @return bool If file is not larger than allowed
	 */
	protected function checkFilesize($uid) {
		if (filesize($_FILES['tx_powermail_pi1']['tmp_name']['field'][$uid]) <= $this->settings['misc.']['file.']['size']) {
			return TRUE;
		}
		$this->addError('upload_size', $uid);
		$this->isValid = FALSE;
		return FALSE;
	}

	/**
	 * Check extension of given filename
	 *
	 * @param string $filename Filename like (upload.txt)
	 * @param int $uid Field uid
	 * @return bool If Extension is allowed via ts
	 */
	protected function checkExtension($filename, $uid) {
		$fileInfo = pathinfo($filename);
		if (
			!empty($fileInfo['extension']) &&
			!empty($this->settings['misc.']['file.']['extension']) &&
			t3lib_div::inList($this->settings['misc.']['file.']['extension'], strtolower($fileInfo['extension'])) &&
			t3lib_div::verifyFilenameAgainstDenyPattern($filename)
		) {
			return TRUE;
		}
		$this->addError('upload_extension', $uid);
		$this->isValid = FALSE;
		return FALSE;
	}

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$typoScriptSetup = $this->configurationManager->getConfiguration(
			Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
		);
		$this->settings = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->basicFileFunctions = t3lib_div::makeInstance('t3lib_basicFileFunctions');
	}

	/**
	 * injectFieldsRepository
	 *
	 * @param Tx_Powermail_Domain_Repository_FieldsRepository $fieldsRepository
	 * @return void
	 */
	public function injectFieldsRepository(Tx_Powermail_Domain_Repository_FieldsRepository $fieldsRepository) {
		$this->fieldsRepository = $fieldsRepository;
	}

	/**
	 * @param Tx_Extbase_SignalSlot_Dispatcher $signalSlotDispatcher
	 * @return void
	 */
	public function injectSignalSlotDispatcher(Tx_Extbase_SignalSlot_Dispatcher $signalSlotDispatcher) {
		$this->signalSlotDispatcher = $signalSlotDispatcher;
	}
}
