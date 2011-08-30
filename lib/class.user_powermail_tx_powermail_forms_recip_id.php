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
 * Class/Function which manipulates the item-array for table/field tx_powermail_forms_recip_id.
 *
 * @author	Alex Kellner, Mischa Heißmann <alexander.kellner@einpraegsam.net, typo3.2009@heissmann.org>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class user_powermail_tx_powermail_forms_recip_id {

	// function main() lists email addresses of chosen tables
	function main(&$params, &$pObj) {

		if (!empty($params['row']['tx_powermail_recip_table'])) { // if a table was selected in flexform

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( // db query - get all of selected table
				$select = '*', // get all fields
				$from = $params['row']['tx_powermail_recip_table'], // of selected table
				$where = '1 AND deleted = 0', // where clause
				$groupBy = '',
				$orderBy = '',
				$limit = '100000' // limit for performance reasons
			);
			if ($res) { // if there is a result

				/*
				// TODO: Select a group and send mail to whole group
				if (preg_match('/group/', $params['row']['tx_powermail_recip_table'])) { // if chosen table contents "group"
					
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // one loop for every record
						$params['items'][] = array($pObj->sL($row['title']), $row['uid']);
					}
				
				} else { // chosen table don't contents "group" in its name
				*/

				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // one loop for every record
					$uid = $row['uid']; // get uid of current record
					foreach ((array)$row as $k => $v) { // one loop for every field of current record
						if (t3lib_div::validEmail($v)) { // if current fieldvalue is an email address
							$params['items'][] = array($pObj->sL($v), $v); // add email to array for returning
						}
					}
				}
			}
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_recip_id.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_recip_id.php']);
}
?>