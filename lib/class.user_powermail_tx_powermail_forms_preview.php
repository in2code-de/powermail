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
 * Class/Function which manipulates the item-array for table/field tx_powermail_forms_recip_field.
 *
 * @author	Mischa Heißmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class user_powermail_tx_powermail_forms_preview {
	function main(&$params, &$pObj) {

		//$rootfolder = substr($_SERVER['SCRIPT_NAME'], 0 , strpos($_SERVER['SCRIPT_NAME'],'/typo3'));
		//$http_host = 'http://'.$GLOBALS['_SERVER']['HTTP_HOST'].$rootfolder.'/index.php?id='.$params['row']['pid'].'&no_cache=1';
		$http_host = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . 'index.php?id=' . $params['row']['pid'] . '&no_cache=1';
		return '<iframe src="' . $http_host . '" style="width:600px;height:350px;border:1px solid black;background-color: white; margin: 10px 20px 10px 0px;" name="powermail_preview"></iframe>';

	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_preview.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_forms_preview.php']);
}
?>