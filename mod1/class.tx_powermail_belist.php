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

require_once('../lib/class.tx_powermail_functions_div.php');

class tx_powermail_belist {

	/**
	 * Timeformat for displaying date
	 *
	 * @var string
	 */
	var $timeformat = 'Y-m-d H:i';
	
	/**
	 * Show X entries per page
	 *
	 * @var int
	 */
	var $perpage = 100;
	
	/**
	 * Pointer for pagebrowser
	 *
	 * @var int
	 */
	var $pointer = 0;
	
	/**
	 * $LANG object
	 *
	 * @var language
	 */
	var $LANG = null;
	
	/**
	 * Functions div object
	 *
	 * @var tx_powermail_functions_div
	 */
	var $divfunctions = null;
	
	/**
	 * PageTSConfig
	 *
	 * @var array
	 */
	var $tsconfig = array();
	
	/**
	 * PageId
	 *
	 * @var int
	 */
	var $pid = 0;
	
	/**
	 * Mail ID
	 *
	 * @var int
	 */
	var $mailID = 0;
	
	/**
	 * TYPO3 $BACK_PATH
	 *
	 * @var string
	 */
	var $backpath = '';
	
	/**
	 * Content to output
	 *
	 * @var string
	 */
	var $content = '';
	
	/**
	 * Startdate for filterform
	 *
	 * @var string
	 */
	var $startdate = '';
	
	/**
	 * Enddate for filterform
	 *
	 * @var string
	 */
	var $enddate = '';
	
	/**
	 * Timeformat for start
	 *
	 * @var string
	 */
	var $timeformat_start = '';
	
	/**
	 * Number of all rows
	 *
	 * @var int
	 */
	var $num = 0;
	
	/**
	 * Number of rows with filter
	 *
	 * @var int
	 */
	var $num2 = 0;
	
	/**
	 * Main method
	 *
	 * @param int $pid
	 * @param string $BACK_PATH
	 * @param int $mailID
	 * @param int $noHeader
	 * @return string
	 */
	function main($pid, $BACK_PATH = '', $mailID = 0, $noHeader = 0) {
		
		$this->pid = $pid;
		$this->backpath = $BACK_PATH;
		$this->mailID = $mailID;
		$this->content = '';
		$this->tsconfig = t3lib_BEfunc::getModTSconfig($this->pid, 'tx_powermail_mod1');
		$this->divfunctions = t3lib_div::makeInstance('tx_powermail_functions_div');
		
		// Get enddate for filter from tsconfig or if not set take current time
		$this->enddate = date('Y-m-d H:i', time());
		if(!empty($this->tsconfig['properties']['config.']['list.']['filterend'])) {
			$this->enddate = $this->tsconfig['properties']['config.']['list.']['filterend'];
		}

		// Get filter start from tsconfig or if not set take first of current month
		$this->timeformat_start = date('Y-m-01 00:00', time());
		if(!empty($this->tsconfig['properties']['config.']['list.']['filterstart'])) {
			$this->timeformat_start = $this->tsconfig['properties']['config.']['list.']['filterstart']; 
		}
		
		// Get dateformat from tsconfig
		if(!empty($this->tsconfig['properties']['config.']['list.']['dateformat'])){
			$this->timeformat = $this->tsconfig['properties']['config.']['list.']['dateformat']; 
		}
		
		// Get current start date
		$this->startdate = $this->timeformat_start;
		if(isset($_GET['startdate'])){
			$this->startdate = t3lib_div::_GET('startdate');
		}
		
		// Get current stop date
		if(isset($_GET['enddate'])){
			$this->enddate = t3lib_div::_GET('enddate'); 
		}
		
		// Get current pointer
		if(isset($_GET['pointer'])) {
			$this->pointer = intval(t3lib_div::_GET('pointer')); 
		}
		
		// Get hits per page if set per tsconfig		
		if($this->tsconfig['properties']['config.']['list.']['perPage'] > 0) {
			$this->perpage = intval($this->tsconfig['properties']['config.']['list.']['perPage']); 
		}
		
		// Count numbers of all entries
		$where_add = '';
		if ($this->mailID > 0) {
			$where_add = ' AND uid = ' . intval($this->mailID);
		}
		
		$select = '*';
		$from = 'tx_powermail_mails';
		$where = '
			pid = ' . intval($this->pid) . ' 
			AND crdate > ' . strtotime($this->startdate) . ' 
			AND crdate < ' . strtotime($this->enddate) . ' 
			AND hidden = 0 
			AND deleted = 0' . 
			$where_add;
		$orderBy = 'crdate DESC';	
		$groupBy = $limit = '';
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		$this->num = $GLOBALS['TYPO3_DB']->sql_num_rows($res1);
		
		// Get entries
		$limit = $this->pointer . ',' . $this->perpage;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		
		// If on current page is a result
		if ($res) {
			$this->num2 = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
			
			// If mailID was not set per GET params AND if header is allowed to show (don't need header in chart view)
			if (!isset($_GET['mailID']) && !$noHeader) { 
				$this->content .= $this->inputFields();
				$this->content .= $this->exportIcons();
			}
			
			$this->content .= '
				<table style="background-color: #7d838c;" border="1" cellpadding="0" cellspacing="0">
					<tr>
						<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_no') . '</b></td>
						<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_date') . '</b></td>
						<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_sender') . '</b></td>
						<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_receiver') . '</b></td>
						<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_IP') . '</b></td>
						<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_details') . '</b></td>
						<td><b style="color: white; padding: 0 5px;">' . $this->LANG->getLL('title_delete') . '</b></td>
					</tr>' . "\n";
			
			$i = $this->pointer;
			
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$i++;
				
				if($_GET['mailID'] > 0) {
					$imgSrc = $this->backpath . 'gfx/i/pages.gif';
					if(is_file($this->backpath . 'sysext/t3skin/icons/gfx/i/pages.gif')) {
						$imgSrc = $this->backpath . 'sysext/t3skin/icons/gfx/i/pages.gif';
					}
					$detailContent = '<img src="' . $imgSrc . '" title="Not available in detail view" alt="detail" />';
					
				}else{
					$imgSrc = $this->backpath . 'gfx/zoom.gif';
					if (is_file($this->backpath . 'sysext/t3skin/icons/gfx/zoom.gif')) {
						$imgSrc = $this->backpath . 'sysext/t3skin/icons/gfx/zoom.gif';
					}
					
					$detailContent = '
						<a href="index.php?id=' . $pid . '&mailID=' . $row['uid'] . '" onclick="vHWin=window.open(\'index.php?id=' . $pid . '&mailID=' . $row['uid'] . '\',\'FEopenLink\',\'width=600,height=600,scrollbars=yes,resize=yes\');vHWin.focus();return false;">
							<img src="' . $imgSrc . '" title="Open mail details" alt="detail" />
						</a>';
				}
				
				$this->content .= '
					<tr>
						<td style="color: white; padding: 0 5px;">' . $i . '.</td>
						<td style="color: white; padding: 0 5px;">' . date($this->timeformat, $row['crdate']) . '</td>
						<td style="color: white; padding: 0 5px;">' . $this->divfunctions->linker($row['sender'],' style="color: white; text-decoration: underline;"') . '</td>
						<td style="color: white; padding: 0 5px;">' . $this->divfunctions->linker($row['recipient'],' style="color: white; text-decoration: underline;"') . '</td>
						<td style="color: white; padding: 0 5px;">' . $row['senderIP'] . '</td>
						<td style="color: white; padding: 0 5px; text-align: center;">' . $detailContent . '</td>
						<td style="color: white; padding: 0 5px; text-align: center;"><a href="index.php?id=' . $pid . '&deleteID=' . $row['uid'] . ($_GET['startdate'] ? '&startdate=' . $_GET['startdate'] : '') . ($_GET['enddate'] ? '&enddate=' . $_GET['enddate'] : '') . '" onclick="return confirmSubmit(this)"><img src="' . $this->backpath . 'sysext/t3skin/icons/gfx/garbage.gif" title="Delete this entry" alt="delete" /></a>' . '</td>
					</tr>' . "\n";
			}
			
			$this->content .= '
				</table>' . "\n";
			
			// Show pagebrowser below table
			if (!isset($_GET['mailID'])) {
				$this->content .= $this->pageBrowser($this->num, $this->num2, $this->pointer, $this->perpage);
			}
		}
		
		// If on current page is no result
		if (!$i) {
			
			// if no mailId and header should be displayed
			$this->content = '';
			if (!isset($_GET['mailID']) && !$noHeader && $this->num == 0) { 
				$this->content = $this->inputFields(); // Show input fields for date filter
			}
			
			$this->content .= '
				<br />
				<br />
				<br />
				<br />
				<strong>' . $this->LANG->getLL('nopowermails1') . '</strong>
				<br />' .
				$this->LANG->getLL('nopowermails2') . '
				<br />';
		}
		
		return $this->content;
	}
	
	/**
	 * Pagebrowser function
	 *
	 * @param int $num
	 * @param int $numf
	 * @param int $a
	 * @param int $b
	 * @return string
	 */
	function pageBrowser($num = 1000, $numf = 10, $a = 0, $b = 10) {
		$n = $a + 1;
		$m = $a + $numf;

		$content = '<br /><br />' . $n . ' ' . $this->LANG->getLL('pagebrowser_upto') . ' ' . $m . ' ' . $this->LANG->getLL('pagebrowser_within') . ' ' . $num . '<br /><br />';
		$pointer = 0;

		// Generate startdate parameter
		$startDate = '';
		if(isset($_GET['startdate'])) {
			$startDate = '&startdate='.$this->startdate;
		}
		
		// Generate enddate parameter
		$endDate = '';
		if(isset($_GET['enddate'])) {
			$endDate = '&enddate=' . $this->enddate;
		}
		
		for($x = 0; $x < ceil($num / $b); $x++) {
			$y = $x + 1;
			if($pointer == t3lib_div::_GET('pointer')) {
				$page = '<strong>' . $this->LANG->getLL('pagebrowser_page') . ' ' . $y . '</strong>';
			} else {
				$page = $this->LANG->getLL('pagebrowser_page') . ' ' . $y;
			}

			$content .= '
				<a href="index.php?id=' . $this->pid . '&pointer=' . $pointer . $startDate . $endDate . '">
					' . $page . '
				</a> :: ';
			$pointer = $pointer + $b;
		}
		$content = substr($content,0,-4); // delete last ::
		
		return $content;
	}

	
	/**
	 * Show input fields for filtering
	 *
	 * @return string
	 */
	function inputFields() {
		$content = '
			<div style="float: left;">' . "\n" .
				'<label for="startdate" style="font-weight: bold; display: block; float: left; width: 50px;">' . $this->LANG->getLL('filter_start') . ':</label>
				<input type="text" name="startdate" id="startdate" value="' . $this->startdate . '" /><br />' . "\n" .
				'<label for="enddate" style="font-weight: bold; display: block; float: left; width: 50px; clear: both;">' . $this->LANG->getLL('filter_end') . ':</label>
				<input type="text" name="enddate" id="enddate" value="' . $this->enddate . '" />' . "\n";
				
		if(isset($_GET['id'])) {
			$content .= '<input type="hidden" name="id" value="' . intval(t3lib_div::_GET('id')) . '" />' . "\n";
		}
		
		$content .= '
				<input type="submit" value="Filter" />' . "\n" .
			'</div>' . "\n";
		
		return $content;
	}
	
	/**
	 * Show links for export methods
	 *
	 * @return string
	 */
	function exportIcons() {
		// Delete records after export ?
		$deleteAfterExport = '';
		if(t3lib_div::_GET('delafterexport') == 1){
			$deleteAfterExport = '&delafterexport=1';
		}
		
		$startDate = urlencode($this->startdate);
		$endDate = urlencode($this->enddate);
		
		$content = '
			<div style="float: right;">
				<a href="index.php?id=' . $this->pid . '&export=xls&startdate=' . $startDate . '&enddate=' . $endDate . $deleteAfterExport . '">
					<img src="../img/icon_xls.gif" style="margin: 5px;" title="' . $this->LANG->getLL('export_icon_excel') . '" alt="XLS export" />
				</a>
				<a href="index.php?id=' . $this->pid . '&export=csv&startdate=' . $startDate . '&enddate=' . $endDate . $deleteAfterExport . '">
					<img src="../img/icon_csv.gif" style="margin: 5px;" title="' . $this->LANG->getLL('export_icon_csv') . '" alt="CSV export" />
				</a>
				<a href="index.php?id=' . $this->pid . '&export=table&startdate=' . $startDate . '&enddate=' . $endDate . $deleteAfterExport . '" target="_blank">
					<img src="../img/icon_table.gif" style="margin: 5px;" title="' . $this->LANG->getLL('export_icon_html') . '" alt="HTML export" />
				</a>';
		
		// If delete after export is disabled
		if ($deleteAfterExport == '') {
			$content .= '
				<a href="index.php?id=' . $this->pid . '&delafterexport=1&startdate=' . $startDate . '&enddate=' . $endDate . '" onclick="if (confirm(\'' . $this->LANG->getLL('delall_sure') . '\')) return true; else return false;">
					<img src="../img/icon_deloff.gif" style="margin: 5px;" title="' . $this->LANG->getLL('icon_deloff') . '" alt="deleteOff" />
				</a>';
			
		} else {
			$content .= '
				<a href="index.php?id=' . $this->pid . '&delafterexport=0&startdate=' . $startDate . '&enddate=' . $endDate . '">
					<img src="../img/icon_delon.gif" style="margin: 5px;" title="' . $this->LANG->getLL('icon_delon') . '" alt="deleteOn" />
				</a>';		
		}
		
		$content .= '
			</div>
			<div style="clear: both;"></div>
			<br />';
		
		return $content;
	}
	
	/**
	 * Init method
	 * Setting class attributes
	 *
	 * @param lang $LANG
	 */
	function init($LANG) {
		$this->LANG = $LANG;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_belist.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_belist.php']);
}
?>