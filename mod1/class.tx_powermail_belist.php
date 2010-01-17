<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Alex Kellner, Mischa Heissmann <alexander.kellner@einpraegsam.net, typo3.YYYY@heissmann.org>
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

require_once('../lib/class.tx_powermail_functions_div.php'); // include div functions

class tx_powermail_belist {

	var $timeformat = 'Y-m-d H:i'; // timeformat for displaying date
	var $perpage = 100; // show X entries per page
	var $pointer = 0; // pointer for pagebrowser
	
	// Function Main
	function main($pid, $BACK_PATH = '', $mailID = 0, $noHeader = 0) {
		
		// config
		$this->pid = $pid;
		$this->backpath = $BACK_PATH;
		$this->mailID = $mailID;
		$this->content = '';
		$this->tsconfig = t3lib_BEfunc::getModTSconfig($this->pid, 'tx_powermail_mod1'); // Get tsconfig from current page
		$this->divfunctions = t3lib_div::makeInstance('tx_powermail_functions_div'); // make instance with dif functions
		
		// settings of values
		(!empty($this->tsconfig['properties']['config.']['list.']['filterend']) ? $this->enddate = $this->tsconfig['properties']['config.']['list.']['filterend'] : $this->enddate = date('Y-m-d H:i', time()) ); // Get enddate for filter from tsconfig or if not set take current time
		(!empty($this->tsconfig['properties']['config.']['list.']['filterstart']) ? $this->timeformat_start = $this->tsconfig['properties']['config.']['list.']['filterstart'] : $this->timeformat_start = date('Y-m-01 00:00', time()) ); // Get filter start from tsconfig or if not set take first of current month
		(!empty($this->tsconfig['properties']['config.']['list.']['dateformat']) ? $this->timeformat = $this->tsconfig['properties']['config.']['list.']['dateformat'] : '' ); // Get dateformat from tsconfig
		(isset($_GET['startdate']) ? $this->startdate = $_GET['startdate'] : $this->startdate = $this->timeformat_start ); // Get current start date
		(isset($_GET['enddate']) ? $this->enddate = $_GET['enddate'] : '' ); // Get current stop date
		(isset($_GET['pointer']) ? $this->pointer = intval($_GET['pointer']) : '' ); // Get current pointer
		($this->tsconfig['properties']['config.']['list.']['perPage'] > 0 ? $this->perpage = intval($this->tsconfig['properties']['config.']['list.']['perPage']) : '' ); // Get hits per page if set per tsconfig		
		
		// DB query
		// 1. get numbers of all entries
		if ($this->mailID > 0) $where_add = ' AND uid = ' . intval($this->mailID); else $where_add = '';
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'*',
			'tx_powermail_mails',
			$where_clause = 'pid = ' . intval($this->pid) . ' AND crdate > ' . strtotime($this->startdate) . ' AND crdate < ' . strtotime($this->enddate) . ' AND hidden = 0 AND deleted = 0' . $where_add,
			$groupBy = '',
			$orderBy = 'crdate DESC',
			$limit = ''
		);
		$this->num = $GLOBALS['TYPO3_DB']->sql_num_rows($res1); // numbers of all entries
		
		// 2. get entries
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'*',
			'tx_powermail_mails',
			$where_clause = 'pid = ' . intval($this->pid) . ' AND crdate > ' . strtotime($this->startdate) . ' AND crdate < ' . strtotime($this->enddate) . ' AND hidden = 0 AND deleted = 0' . $where_add,
			$groupBy = '',
			$orderBy = 'crdate DESC',
			$limit = $this->pointer . ',' . $this->perpage
		);
		if ($res) { // If on current page is a result
			$this->num2 = $GLOBALS['TYPO3_DB']->sql_num_rows($res); // numbers of all entries
			if (!isset($_GET['mailID']) && !$noHeader) { // If mailID was not set per GET params AND if header is allowed to show (don't need header in chart view)
				$this->content .= $this->inputFields(); // Show input fields for date filter
				$this->content .= $this->exportIcons(); // Show export images
			}
			$this->content .= '<table style="background-color: #7d838c;" border="1" cellpadding="0" cellspacing="0">';
			$this->content .= '
				<tr>
					<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_no') . '</b></td>
					<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_date') . '</b></td>
					<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_sender') . '</b></td>
					<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_receiver') . '</b></td>
					<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_IP') . '</b></td>
					<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_details') . '</b></td>
					<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_delete') . '</b></td>
				</tr>' . "\n"
			; // write table head
			$i = $this->pointer; // init
			
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // one loop for every db entry
				$i++; // increase
				
				$this->content .= '<tr>';
				$this->content .= '<td style="color: white; padding: 0 5px;">' . $i . '.</td>'; // #
				$this->content .= '<td style="color: white; padding: 0 5px;">' . date($this->timeformat, $row['crdate']) . '</td>'; // date
				$this->content .= '<td style="color: white; padding: 0 5px;">' . $this->divfunctions->linker($row['sender'],' style="color: white; text-decoration: underline;"') . '</td>'; // sender email
				$this->content .= '<td style="color: white; padding: 0 5px;">' . $this->divfunctions->linker($row['recipient'],' style="color: white; text-decoration: underline;"') . '</td>'; // receiver email
				$this->content .= '<td style="color: white; padding: 0 5px;">' . $row['senderIP'] . '</td>'; // sender IP
				$this->content .= '<td style="color: white; padding: 0 5px; text-align: center;">' . ($_GET['mailID'] > 0 ? '<img src="' . (is_file($this->backpath . 'sysext/t3skin/icons/gfx/i/pages.gif') ? $this->backpath . 'sysext/t3skin/icons/gfx/i/pages.gif' : $this->backpath . 'gfx/i/pages.gif') . '" title="Not available in detail view" alt="detail" />' : '<a href="index.php?id=' . $pid . '&mailID=' . $row['uid'] . '" onclick="vHWin=window.open(\'index.php?id=' . $pid . '&mailID=' . $row['uid'] . '\',\'FEopenLink\',\'width=600,height=600,scrollbars=yes,resize=yes\');vHWin.focus();return false;"><img src="' . (is_file($this->backpath . 'sysext/t3skin/icons/gfx/zoom.gif') ? $this->backpath . 'sysext/t3skin/icons/gfx/zoom.gif' : $this->backpath . 'gfx/zoom.gif') . '" title="Open mail details" alt="detail" /></a>') . '</td>';
				$this->content .= '<td style="color: white; padding: 0 5px; text-align: center;"><a href="index.php?id=' . $pid . '&deleteID=' . $row['uid'] . ($_GET['startdate'] ? '&startdate=' . $_GET['startdate'] : '') . ($_GET['enddate'] ? '&enddate=' . $_GET['enddate'] : '') . '" onclick="return confirmSubmit(this)"><img src="' . $this->backpath . 'sysext/t3skin/icons/gfx/garbage.gif" title="Delete this entry" alt="delete" /></a>' . '</td>';
				
				$this->content .= '</tr>' . "\n";
			}
			$this->content .= '</table>' . "\n";
			if (!isset($_GET['mailID'])) $this->content .= $this->pageBrowser($this->num, $this->num2, $this->pointer, $this->perpage); // show pagebrowser below table
		}
		
		if (!$i) { // if on current page is no result
			if (!isset($_GET['mailID']) && !$noHeader && $this->num == 0) { // if no mailId and header should be displayed
				$this->content = $this->inputFields(); // Show input fields for date filter
			} else {
				$this->content = ''; // clear content
			}
			$this->content .= '<br /><br /><br /><br /><strong>' . $this->LANG->getLL('nopowermails1') . '</strong><br />';
			$this->content .= $this->LANG->getLL('nopowermails2') . '<br />';
		}
		
		return $this->content; // return
	}
	
	// Pagebrowser function
	function pageBrowser($num = 1000, $numf = 10, $a = 0, $b = 10) {
		$n = $a + 1;
		$m = $a + $numf;

		$content = '<br><br>' . $n . ' ' . $this->LANG->getLL('pagebrowser_upto') . ' ' . $m . ' ' . $this->LANG->getLL('pagebrowser_within') . ' ' . $num . '<br><br>';
		$pointer = 0;

		for($x=0; $x < ceil($num / $b); $x++) {
			$y = $x + 1;
			if($pointer == $_GET['pointer']) {
				$page = '<strong>' . $this->LANG->getLL('pagebrowser_page') . ' ' . $y . '</strong>';
			}
			else {
				$page = $this->LANG->getLL('pagebrowser_page') . ' ' . $y;
			}
			$content .= '<a href="index.php?id=' . $this->pid . '&pointer=' . $pointer . (isset($_GET['startdate']) ? '&startdate='.$this->startdate : '') . (isset($_GET['enddate']) ? '&enddate=' . $this->enddate : '') . '">' . $page . '</a> :: ';
			$pointer = $pointer + $b;
		};
		$content = substr($content,0,-4); // delete last ::
		return $content;
	}

	
	// Show input fields for filtering
	function inputFields() {
		$content = '<div style="float: left;">' . "\n";
		$content .= '<label for="startdate" style="font-weight: bold; display: block; float: left; width: 50px;">' . $this->LANG->getLL('filter_start') . ':</label><input type="text" name="startdate" id="startdate" value="' . $this->startdate . '" /><br />' . "\n";
		$content .= '<label for="enddate" style="font-weight: bold; display: block; float: left; width: 50px; clear: both;">' . $this->LANG->getLL('filter_end') . ':</label><input type="text" name="enddate" id="enddate" value="' . $this->enddate . '" />' . "\n";
		if(isset($_GET['id'])) $content .= '<input type="hidden" name="id" value="' . intval($_GET['id']) . '" />' . "\n";
		$content .= '<input type="submit" value="Filter" />' . "\n";
		$content .= '</div>' . "\n";
		
		return $content;
	}
	
	// Show links for export methods
	function exportIcons() {
		$content = '<div style="float: right;">';
		$content .= '<a href="index.php?id=' . $this->pid . '&export=xls&startdate=' . urlencode($this->startdate) . '&enddate=' . urlencode($this->enddate) . ($_GET['delafterexport'] == 1 ? '&delafterexport=1' : '') . '"><img src="../img/icon_xls.gif" style="margin: 5px;" title="' . $this->LANG->getLL('export_icon_excel') . '" alt="XLS export" /></a>';
		$content .= '<a href="index.php?id=' . $this->pid . '&export=csv&startdate=' . urlencode($this->startdate) . '&enddate=' . urlencode($this->enddate) . ($_GET['delafterexport'] == 1 ? '&delafterexport=1' : '') . '"><img src="../img/icon_csv.gif" style="margin: 5px;" title="' . $this->LANG->getLL('export_icon_csv') . '" alt="CSV export" /></a>';
		$content .= '<a href="index.php?id=' . $this->pid . '&export=table&startdate=' . urlencode($this->startdate) . '&enddate=' . urlencode($this->enddate) . ($_GET['delafterexport'] == 1 ? '&delafterexport=1' : '') . '" target="_blank"><img src="../img/icon_table.gif" style="margin: 5px;" title="' . $this->LANG->getLL('export_icon_html') . '" alt="HTML export" /></a>';
		if ($_GET['delafterexport'] != 1) { // off
			$content .= '<a href="index.php?id=' . $this->pid . '&delafterexport=1&startdate=' . urlencode($this->startdate) . '&enddate=' . urlencode($this->enddate) . '" onclick="if (confirm(\'' . $this->LANG->getLL('delall_sure') . '\')) return true; else return false;"><img src="../img/icon_deloff.gif" style="margin: 5px;" title="' . $this->LANG->getLL('icon_deloff') . '" alt="deleteOff" /></a>';
		} else { // on
			$content .= '<a href="index.php?id=' . $this->pid . '&delafterexport=0&startdate=' . urlencode($this->startdate) . '&enddate=' . urlencode($this->enddate) . '"><img src="../img/icon_delon.gif" style="margin: 5px;" title="' . $this->LANG->getLL('icon_delon') . '" alt="deleteOn" /></a>';		
		}
		$content .= '</div>';
		$content .= '<div style="clear: both;"></div>';
		$content .= '<br />';
		
		return $content;
	}
	
	// Init
	function init($LANG) {
		$this->LANG = $LANG; // make $LANG global
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_belist.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_belist.php']);
}
?>