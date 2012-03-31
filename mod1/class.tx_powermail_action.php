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
 *   69:     function main($deleteID, $LANG)
 *   93:     function deleteFiles()
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
	 * $LANG object
	 *
	 * @var	language
	 */
	var $LANG = null;

	/**
	 * Content to output
	 *
	 * @var	string
	 */
	var $content = '';

	/**
	 * Method main() to set powermail mail entries as deleted in the database
	 *
	 * @param	int		$deleteID
	 * @param	lang		$LANG
	 * @return	string
	 */
	function main($deleteID, $LANG) {
		$this->LANG = $LANG;

		$this->content = '
			<div style="margin: 10px 0; padding: 10px; border: 1px solid #7D838C; background-color: green; color: white;">
				<strong>'.sprintf($this->LANG->getLL('del_message'), $deleteID).'</strong>
			</div>';

		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_powermail_mails',
			'uid = ' . intval($deleteID),
			array (
				'deleted' => 1
			)
		);

		return $this->content;
	}

	/**
	 * Method deleteFiles() delete old temp files
	 *
	 * @return	void
	 */
	function deleteFiles() {
		$fileArray = t3lib_div::getFilesInDir(t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT').'/typo3temp/', 'csv,gz', 1, 1);

		foreach ((array) $fileArray as $value) {
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