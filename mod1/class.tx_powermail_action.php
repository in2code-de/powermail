<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Alexander Kellner, Mischa Heissmann <alexander.kellner@einpraegsam.net, typo3.2008@heissmann.org>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

class tx_powermail_action {

	// Function main() to set powermailmail entries as deleted in the database
	function main($deleteID, $LANG) {
		// config
		$this->mailID = $mailID;
		$this->LANG = $LANG;
		$this->content = '<div style="margin: 10px 0; padding: 10px; border: 1px solid #7D838C; background-color: green; color: white;"><strong>'.sprintf($this->LANG->getLL('del_message'), $deleteID).'</strong></div>';
		
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery ( // deleted = 1 in db
			'tx_powermail_mails',
			'uid = '.$deleteID,
			array (
				'deleted' => 1
			)
		);
		
		return $this->content; // return message
	}
	
	
	// Function deleteFiles() delete old temp files
	function deleteFiles() {
		$fileArray = t3lib_div::getFilesInDir(t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT').'/typo3temp/', 'csv,gz', 1, 1); // file array of all csv and gz files in the typo3temp directory
		
		foreach ((array) $fileArray as $key => $value) { // one loop for every file
			if (strpos($value, 'powermail_export') !== false) { // string powermail_export found in current name
				unlink($value); // delte current file
			}
		}
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_action.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_action.php']);
}
?>