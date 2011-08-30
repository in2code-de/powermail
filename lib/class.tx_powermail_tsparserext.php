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
 * Class that renders fields for the extension manager configuration
 *
 * @author	 Alexander Grein <ag@mediaessenz.eu>
 * @package	TYPO3
 * @subpackage tx_powermail
 */
class tx_powermail_tsparserext {
	/**
	 * Shows the update Message
	 *
	 * @return	string
	 */
	function displayMessage(&$params, &$tsObj) {
		$out = '';

		if (t3lib_div::int_from_ver(TYPO3_version) < 4003000) {
			// 4.3.0 comes with flashmessages styles. For older versions we include the needed styles here
			$cssPath = $GLOBALS['BACK_PATH'] . t3lib_extMgm::extRelPath('powermail');
			$out .= '<link rel="stylesheet" type="text/css" href="' . $cssPath . 'res/css/flashmessages.css" media="screen" />';
		}

		$checkConfig = null;
		if ($this->checkConfig() === false) {
			$checkConfig = '
	<div class="typo3-message message-warning">
		<div class="message-header">' . $GLOBALS['LANG']->sL('LLL:EXT:powermail/locallang.xml:extmng.classInnerHeader') . '</div>
		<div class="message-body">
			' . $GLOBALS['LANG']->sL('LLL:EXT:powermail/locallang.xml:extmng.classInner') . '
		</div>
	</div>';
		}

		if (t3lib_div::int_from_ver(TYPO3_version) < 4005000) {
			$url = 'index.php?&amp;id=0&amp;CMD[showExt]=powermail&amp;SET[singleDetails]=updateModule';
		} else {
			$url = 'mod.php?&id=0&M=tools_em&CMD[showExt]=powermail&SET[singleDetails]=updateModule';
		}

		if (count($GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid', 'sys_log', 'type = 4 AND details LIKE \'%updateStoragePage%\'' . t3lib_BEfunc::deleteClause('sys_log'))) == 0) {
			$out .= '
<div style="position:absolute;top:10px;right:10px; width:300px;">
	<div class="typo3-message message-information">
		<div class="message-header">' . $GLOBALS['LANG']->sL('LLL:EXT:powermail/locallang.xml:extmng.updatermsgHeader') . '</div>
		<div class="message-body">
			' . $GLOBALS['LANG']->sL('LLL:EXT:powermail/locallang.xml:extmng.updatermsg') . '<br />
			<a style="text-decoration:underline;" href="' . $url . '">
			' . $GLOBALS['LANG']->sL('LLL:EXT:powermail/locallang.xml:extmng.updatermsgLink') . '</a>
		</div>
	</div>
	' . $checkConfig . '
</div>';
		}

		return $out;
	}

	/**
	 * Check the config for a given feature
	 *
	 * @return boolean
	 */
	function checkConfig() {
		$confDefault = array(
			'usePreview',
			'cssSelection',
			'feusersPrefill',
			'disableIPlog',
			'disableBackendModule',
			'disableStartStop',
			'useIRRE',
			'fileToolPath',
		);
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
		foreach ($confDefault as $val) {
			if (!isset($confArr[$val]) && !isset($_POST['data'][$val])) {
				return false;
			}
		}
		return true;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_tsparserext.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_tsparserext.php']);
}
?>