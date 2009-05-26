<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Mischa Hei√ümann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@wunschtacho.de>
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
 * @author	Mischa Heissmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class user_powermail_tx_powermail_forms_recip_table {
	
	function main(&$params,&$pObj)	{							
		$tables = $GLOBALS['TYPO3_DB']->admin_get_tables();
		
		if(isset($tables) && is_array($tables)) {
			if(t3lib_div::int_from_ver(TYPO3_version) < t3lib_div::int_from_ver('4.2.0')) {
				foreach($tables as $v) {
					$params['items'][] = array($pObj->sL($v),$v);
				}
			}
			else {
				foreach($tables as $k => $v) {
					$params['items'][] = array($pObj->sL($k),$k);
				}
			}
		}
		
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_recip_table.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_recip_table.php']);
}

?>