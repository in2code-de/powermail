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
 * Function user_powermailOnCurrentPage() checks if a powermail plugin is inserted on current page
 *
 * @param	string		$mode: mode could be empty or "ssd"
 * @return	boolean		0/1
 */
function user_powermailOnCurrentPage($mode = '') {
	$result = FALSE;
	if ($mode == '1') { return true; }

	if (TYPO3_MODE == 'FE') {
		$ttContentWhere = 'AND deleted = 0 AND hidden = 0';
		if (is_array($GLOBALS['TCA']['tt_content']) && method_exists($GLOBALS['TSFE']->sys_page, 'enableFields')) {
			$ttContentWhere = $GLOBALS['TSFE']->sys_page->enableFields('tt_content');
		}

		$pid = getCorrectPageIdForPowermailOnCurrentPageUserfunc();
		$where = 'pid = ' . intval($pid) . ' AND (CType = "powermail_pi1"  OR CType = "shortcut")' . $ttContentWhere;
		$orderBy = 'CType';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('uid, CType, records', 'tt_content', $where, '', $orderBy, '');

		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			switch($row['CType']) {
					// Normal content element
				case 'powermail_pi1':
					$result = isPowermailOnCurrentPage($mode, $row['uid']);
					break;

					// Content element "Insert plugin"
				case 'shortcut':
					$recordUids = array();
					$records = t3lib_div::trimExplode(',', $row['records'], TRUE);
					foreach ($records as $record) {
						$recordInfo = t3lib_BEfunc::splitTable_Uid($record);
						if ($recordInfo[0] === 'tt_content') {
							$recordUids[] = $recordInfo[1];
						}
					}
					$recordUids = $GLOBALS['TYPO3_DB']->cleanIntList(implode(',', $recordUids));

					if(!$recordUids) {
						break;
					}

					$where = 'uid IN ( ' . $recordUids . ' ) AND CType = "powermail_pi1"' . $ttContentWhere;
					$shortcutRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tt_content', $where, '', '', 1);
					$shortcutRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($shortcutRes);
					$result = isPowermailOnCurrentPage($mode, $shortcutRow['uid']);
					break;
			}

			if ($result === TRUE) {
				break;
			}
		}
	}

	return $result;
}

/**
 * Returns the correct Page-ID for the "isPowermailOnCurrentPage"-Check
 * This method has a check for "content_from_pid"-field of pages table
 *
 * @return	integer		Correct PageID
 */
function getCorrectPageIdForPowermailOnCurrentPageUserfunc() {
	$pid = $GLOBALS['TSFE']->id;

		// This part is copied and modified from TSpagegen::pagegenInit();
		// because we can`t call it directly. It is to early.
	if ($GLOBALS['TSFE']->page['content_from_pid'] > 0) {
			// make REAL copy of TSFE object - not reference!
		$temp_copy_TSFE = clone($GLOBALS['TSFE']);
			// Set ->id to the content_from_pid value - we are going to evaluate this pid as was it a given id for a page-display!
		$temp_copy_TSFE->id = $GLOBALS['TSFE']->page['content_from_pid'];
		$temp_copy_TSFE->getPageAndRootlineWithDomain($GLOBALS['TSFE']->tmpl->setup['config.']['content_from_pid_allowOutsideDomain'] ? 0 : $GLOBALS['TSFE']->domainStartPage);
		$pid = intval($temp_copy_TSFE->id);
		unset($temp_copy_TSFE);
	}

	return $pid;
}

function isPowermailOnCurrentPage($mode, $uid) {

	if ((($mode != 'ssd' || $GLOBALS['TSFE']->tmpl->setup['config.']['simulateStaticDocuments'] == 1) && $uid > 0)) {
		return TRUE;
	}

	return FALSE;
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/user_powermailOnCurrentPage.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/user_powermailOnCurrentPage.php']);
}
?>