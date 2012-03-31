<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Alexander Kellner <alexander.kellner@einpraegsam.net>
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

class user_powermail_updateError {
	
	function user_updateError($PA, $fobj) {
		if (strlen($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']) < 1) { // settings for powermail missing
			return '<div style="padding: 5px; background-color: red; color: white;">'.$fobj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.updateError').'</div>';
		} else {
			return $fobj->sL('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.noErrors');
		}
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/user_powermail_updateError.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/user_powermail_updateError.php']);
}
?>