<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Mischa Heißmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@wunschtacho.de>
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
 * @author	Mischa Heißmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class user_powermail_tx_powermail_forms_recip_id {
	function main(&$params,&$pObj)	{
							
		$select_fields = '*';
		$from_table = $params['row']['tx_powermail_recip_table'];
		if($from_table != '0' && $from_table != '') {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$select_fields,
				$from_table,
				$where_clause,
				$groupBy='',
				$orderBy='',
				$limit=''
			);
			if($res != '' || $res > 0) {
			
				if(preg_match('/group/',$params['row']['tx_powermail_recip_table'])) {
					
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						$params['items'][] = array($pObj->sL($row['title']), $row['uid']);
					}
				
				} else {
					
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						$uid = $row['uid'];
						if (isset($row) && is_array($row)) {
							foreach($row as $k => $v){
								if(t3lib_div::validEmail($v)) {
									$params['items'][] = array($pObj->sL($v), $v);
								}
							}
						}
					}
					
				}
				
			}
		}
		
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_recip_id.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_recip_id.php']);
}

?>