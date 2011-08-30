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
 * Class/Function which manipulates the item-array for table/field tx_powermail_fields_fe_field.
 *
 * @author	Mischa Heißmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class user_powermail_tx_powermail_fields_fe_field {

	function main(&$params, &$pObj) {
		// Adding an item!
		$tableName = 'fe_users';
		$res = $GLOBALS['TYPO3_DB']->admin_get_fields($tableName);

		if (isset($res)) {
			$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']); // Get backandconfig
			$AllowedFeuserFields = t3lib_div::trimExplode(',', $confArr['feusersPrefill'], 1); // Allowed feuser fields in an array

			foreach ($res as $k => $v) { // Adding an item!
				$label = '';
				$label = $pObj->sL('LLL:EXT:cms/locallang_tca.xml:fe_users.' . $k);
				if ($label == '') { // if label is still empty
					$label = $pObj->sL('LLL:EXT:lang/locallang_general.php:LGL.' . $k); // get label
				}
				if ($k == 'telephone') { // if key is telephone
					$label = $pObj->sL('LLL:EXT:lang/locallang_general.php:LGL.phone'); // get this label
				}
				if ($label == '') { // if label is still empty
					$label = $k; // take key as label
				}
				if ($label != '' && in_array($k, $AllowedFeuserFields)) { // only if $label is not empty and is allowed
					$params['items'][] = array(preg_replace('/:$/', '', $label), $k);
				}
			}
		}
		// No return - the $params and $pObj variables are passed by reference, so just change content in then and it is passed back automatically...
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_fields_fe_field.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.user_powermail_tx_powermail_fields_fe_field.php']);
}
?>