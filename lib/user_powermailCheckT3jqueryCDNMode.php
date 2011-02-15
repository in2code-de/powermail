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
 * Function user_powermailCheckT3jqueryCDNMode() checks if t3jquery plugin is in CDN mode
 * 
 * @param	string		If string is "false" result will be inverted
 * @return	boolean		0/1
 */

function user_powermailCheckT3jqueryCDNMode($mode = 'true') {
    //global $TYPO3_CONF_VARS;
    if (t3lib_extMgm::isLoaded('t3jquery')) {
        $confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3jquery']);
        //$confArr = $TYPO3_CONF_VARS['EXT']['extConf']['t3jquery'];
        //t3lib_div::devLog('$confArr', 'powermail', 0, $confArr);
        switch($mode){
            case 'false':
                return !($confArr['integrateFromCDN'] == 1);
                break;
            default:
                return ($confArr['integrateFromCDN'] == 1);
        }
    }
    return false;
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/user_powermailCheckT3jqueryCDNMode.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/user_powermailCheckT3jqueryCDNMode.php']);
}
?>