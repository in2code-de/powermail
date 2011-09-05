<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Plugin 'tx_powermail' for the 'powermail' extension.
 *
 * @author	Alex Kellner (alexander.kellner@in2code.de)
 * @package	TYPO3
 * @subpackage	tx_powermail_import_scheduler
 */

class tx_powermail_import_scheduler extends tx_scheduler_Task {

	/**
	 * Function executed from the Scheduler.
	 * Stores CSV data of a given file url to the database table tx_powermail_mails
	 *
	 * @return	bool
	 */
	public function execute() {

		if (intval($this->pid) === 0) {
			$msg = t3lib_div::makeInstance('t3lib_FlashMessage', $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.noPid'), $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.error'), t3lib_FlashMessage::ERROR);
			t3lib_FlashMessageQueue::addMessage($msg);
			return false;
		}

		if (intval($this->formuid) === 0) {
			$msg = t3lib_div::makeInstance('t3lib_FlashMessage', $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.noFormuid'), $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.error'), t3lib_FlashMessage::ERROR);
			t3lib_FlashMessageQueue::addMessage($msg);
			return false;
		}

		switch ($this->delimiter) {
			case 'semicolon':
				$this->delimiter = ';';
				break;
			case 'tab':
				$this->delimiter = '\t';
				break;
			case 'comma':
			default:
				$this->delimiter = ',';
		}

		switch ($this->enclosure) {
			case 'quotation_marks_double':
				$this->enclosure = '"';
				break;
			case 'quotation_marks_single':
				$this->enclosure = '\'';
				break;
			case 'none':
			default:
				$this->enclosure = '';
		}

		$csvFileContent = t3lib_div::getURL($this->fileurl, 0);

		if ($csvFileContent !== false) {
			if (strtolower($this->encoding) != 'utf-8') {
				$csvFileContent = iconv($this->encoding, 'UTF-8', $csvFileContent);
			}
			$csvDataRows = str_getcsv($csvFileContent, "\n"); //parse the rows
			$connectionRows = str_getcsv($this->connections, "\n"); //parse the rows
			$connections = array();
			foreach ($connectionRows as $singleConnection) {
				$connectionRow = t3lib_div::trimExplode('=', $singleConnection);
				$connections[intval($connectionRow[0])] = $connectionRow[1];
			}
			$i = 0;
			foreach ($csvDataRows as $csvDataSingleRow) {
				if ($this->firstline == 1) {
					$this->firstline = 0;
					continue;
				}
				$csvDataSingleRowColumns = str_getcsv($csvDataSingleRow, $this->delimiter, $this->enclosure);
				$importColumns = array();
				$n = 1;
				foreach ($csvDataSingleRowColumns as $value) {
					if (trim($connections[$n]) !== '' && stristr($connections[$n], 'uid') !== false) {
						$importColumns[$connections[$n]] = $value;
					}
					$n++;
				}
				$dbValues = array(
					'pid' => intval($this->pid), // PID
					'tstamp' => time(), // save current time
					'crdate' => time(), // save current time
					'hidden' => 0, // hidden = 0 or hidden = 1
					'formid' => $this->formuid, // save pid
					'recipient' => '', // save receiver mail
					'cc_recipient' => '',
					'subject_r' => '',
					'sender' => '', // save sender mail
					'content' => '', // save content of receiver mail
					'piVars' => t3lib_div::array2xml_cs($importColumns, 'piVars'), // save values from import as xml
					'feuser' => '0', // current feuser id
					'senderIP' => 'no-ip', // save users IP address
					'UserAgent' => 'Powermail scheduler import module', // save user agent
					'Referer' => '', // save referer
					'SP_TZ' => $_SERVER['SP_TZ'], // save sp_tz if available
					'uploadPath' => ''
				);
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_powermail_mails', $dbValues); // DB entry
				unset($importColumns);
				$i++;
				if (!empty($this->limit) && $i == intval($this->limit)) break;
			}
			$msg = t3lib_div::makeInstance('t3lib_FlashMessage', sprintf($GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.successfullyImport'), $i), $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.successfullyImportHeader'), t3lib_FlashMessage::OK);
			t3lib_FlashMessageQueue::addMessage($msg);
			return true;
		}
		$msg = t3lib_div::makeInstance('t3lib_FlashMessage', sprintf($GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.fileNotFound'), $this->fileurl), $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.error'), t3lib_FlashMessage::ERROR);
		t3lib_FlashMessageQueue::addMessage($msg);
		return false;
	}

	/**
	 * his method returns the current csv file url and the storage pid as additional information
	 *
	 * @return	string
	 */
	public function getAdditionalInformation() {
		return sprintf($GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.additionalInformation'), $this->fileurl, $this->pid);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/cli/class.tx_powermail_import_scheduler.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/cli/class.tx_powermail_import_scheduler.php']);
}
?>