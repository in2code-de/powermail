<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Alexander Kellner <alexander.kellner@einpraegsam.net>
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

// Function user_powermailOnCurrentPage() checks if a powermail plugin is inserted on current page
function user_powermailOnCurrentPage($mode) {
	if (TYPO3_MODE == 'FE') { // only in Frontend
		global $TCA;
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // DB query
			'uid',
			'tt_content',
			$where_clause = 'pid = ' . ($GLOBALS['TSFE']->id ? $GLOBALS['TSFE']->id : 0) . ' AND CType = "powermail_pi1"' . (!is_array($TCA['tt_content']) ? 'AND deleted = 0 AND hidden = 0' : $GLOBALS['TSFE']->sys_page->enableFields('tt_content')),
			$groupBy = '',
			$orderBy = '',
			$limit = 1
		);
		if ($res) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res); // Result in array
			
			if ($mode != 'ssd') { // if default or realurl
				if ($row['uid'] > 0) return true;
			} else {
				if ($GLOBALS['TSFE']->tmpl->setup['config.']['simulateStaticDocuments'] == 1 && $row['uid'] > 0) { // if ssd activated
					return true;
				}
			}
		}
	}
	return false;
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/user_powermailOnCurrentPage.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/user_powermailOnCurrentPage.php']);
}
?>