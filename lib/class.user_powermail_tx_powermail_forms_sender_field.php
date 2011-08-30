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
 * Class/Function which manipulates the item-array for the sender-name AND the sender-email field.
 *
 * @author	Alex Kellner, Mischa Heißmann <alexander.kellner@einpraegsam.net, typo3.YYYY@heissmann.org>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class user_powermail_tx_powermail_forms_sender_field {

	/**
	 * Show field values in backend
	 *
	 * @param	array		Field Array
	 * @param	array		Parent Object
	 * @return	void
	 */
	public function main(&$params, &$pObj) {
		$select = 'tx_powermail_fields.uid, tx_powermail_fields.title, tx_powermail_fields.sorting';
		$from = '
			tx_powermail_fieldsets 
			RIGHT JOIN tt_content ON tx_powermail_fieldsets.tt_content = tt_content.uid 
			LEFT JOIN tx_powermail_fields ON tx_powermail_fieldsets.uid = tx_powermail_fields.fieldset
		';
		$where = 'tt_content.uid = \'' . intval($params['row']['uid']) . '\' AND tx_powermail_fields.deleted = 0 AND tx_powermail_fields.hidden = 0';
		$groupBy = 'tx_powermail_fields.uid';
		$orderBy = 'tx_powermail_fields.sorting';
		$limit = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

		if ($res) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				// Adding an item!
				if ($row['uid'] > 0) {
					if ($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] == 'utf-8') { // utf8
						$params['items'][] = array($pObj->sL($row['title']), 'uid' . $row['uid']);
					} else { // no utf8
						$params['items'][] = array($pObj->sL(utf8_decode($row['title'])), utf8_decode('uid' . $row['uid']));
					}
				}
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_sender_field.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_sender_field.php']);
}
?>