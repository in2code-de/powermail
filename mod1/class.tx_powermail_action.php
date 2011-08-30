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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   46: class tx_powermail_action
 *   69:	 function main($deleteID, $LANG)
 *   93:	 function deleteFiles()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * Plugin 'tx_powermail_action' for the 'powermail' extension.
 *
 * @author	powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class tx_powermail_action {

	/**
	 * Method main() to set powermail mail entries as deleted in the database
	 *
	 * @param	int		$deleteID
	 * @return	boolean Returns true if success
	 */
	function deleteItem($uids) {

		$uids_array = json_decode($uids);
		foreach ($uids_array as $uid) {
			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'tx_powermail_mails',
				'uid = ' . intval($uid),
				array(
					 'deleted' => 1
				)
			);
		}
		return $res;
	}

	/**
	 * Method deleteFiles() delete old temp files
	 *
	 * @return	void
	 */
	function deleteFiles() {
		$fileArray = t3lib_div::getFilesInDir(t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/typo3temp/', 'csv,gz', 1, 1);

		foreach ((array)$fileArray as $value) {
			if (strpos($value, 'powermail_export') !== false) {
				unlink($value);
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_action.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_action.php']);
}
?>