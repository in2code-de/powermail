<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Mischa HeiÃŸmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@wunschtacho.de>
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
 * Class/Function which manipulates the item-array for table/field tx_powermail_forms_recip_table.
 *
 * @author	Mischa Heißmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class user_powermail_tx_powermail_forms_sender_field {
	
	function main(&$params,&$pObj)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			$select_fields = 'tx_powermail_fields.uid, tx_powermail_fields.title',
			$from_table = '(tx_powermail_fieldsets RIGHT JOIN tt_content ON tx_powermail_fieldsets.tt_content = tt_content.uid) LEFT JOIN tx_powermail_fields ON tx_powermail_fieldsets.uid = tx_powermail_fields.fieldset',
			$where_clause = 'tt_content.uid = \''.$params['row']['uid'].'\' AND tx_powermail_fields.deleted = 0',
			$groupBy = '',
			$orderBy = '',
			$limit = ''
		);

		if($res != '' || $res > 0) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				// Adding an item!
				if($row['uid'] != '') {
					$params['items'][] = array($pObj->sL(utf8_decode($row['title'])), utf8_decode('uid'.$row['uid']));
				}
			}
		}

		// No return - the $params and $pObj variables are passed by reference, so just change content in then and it is passed back automatically...
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_sender_field.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_sender_field.php']);
}

?>