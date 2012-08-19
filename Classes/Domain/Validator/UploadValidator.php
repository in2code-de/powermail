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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Powermail_Domain_Validator_UploadValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

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
	private $isValid = true;

	/**
	 * Validation of given Params
	 *
	 * @param $params
	 * @return bool
	 */
	public function isValid($field) {
		if (isset($_FILES['tx_powermail_pi1']['name']['field'])) {
			// session stuff
			$uploadSession = array();
			Tx_Powermail_Utility_Div::setSessionValue('upload', array(), true); // clean old session before

			foreach ($_FILES['tx_powermail_pi1']['name']['field'] as $uid => $filename) {

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
				$newFile = $this->basicFileFunctions->getUniqueName($filename, t3lib_div::getFileAbsFileName($this->settings['misc.']['file.']['folder']));
				$uploadSession[] = $newFile; // create array for upload session
				if (!t3lib_div::upload_copy_move($_FILES['tx_powermail_pi1']['tmp_name']['field'][$uid], $newFile)) {
					$this->addError('upload_error', $uid);
					$this->isValid = false;
				}
			}

			// save uploaded filenames to session (to attach it later)
			Tx_Powermail_Utility_Div::setSessionValue('upload', $uploadSession, true);
		}

		return $this->isValid;
  	}

	/**
	 * Check filesize of given file
	 *
	 * @param	int			Field uid
	 * @return	bool		If file is not larger than allowed
	 */
	private function checkFilesize($uid) {
		if (filesize($_FILES['tx_powermail_pi1']['tmp_name']['field'][$uid]) > $this->settings['misc.']['file.']['size']) {
			$this->addError('upload_size', $uid);
			$this->isValid = false;
			return false;
		}
		return true;
	}

	/**
	 * Check extension of given filename
	 *
	 * @param	string		Filename like (upload.txt)
	 * @param	int			Field uid
	 * @return	bool		If Extension is allowed via ts
	 */
	private function checkExtension($filename, $uid) {
		$fileInfo = pathinfo($filename);
		if (!isset($fileInfo['extension']) || !t3lib_div::inList($this->settings['misc.']['file.']['extension'], $fileInfo['extension'])) {
			$this->addError('upload_extension', $uid);
			$this->isValid = false;
			return false;
		}
		return true;
	}

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$typoScriptSetup = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$this->settings = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
	}

	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct() {
		$this->basicFileFunctions = t3lib_div::makeInstance('t3lib_basicFileFunctions');
	}
}

?>