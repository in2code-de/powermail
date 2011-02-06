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
 * Plugin 'tx_powermail_repository' for the 'powermail' extension.
 *
 * @author	powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
 * @package	TYPO3
 * @subpackage	tx_powermail
 */

class tx_powermail_repository {

	/**
	 * Timeformat for displaying date
	 *
	 * @var	string
	 */
	var $timeformat = 'd.m.Y H:i';

	/**
	 * Show X entries per page
	 *
	 * @var	int
	 */
	var $perpage = 100;

	/**
	 * Pointer for pagebrowser
	 *
	 * @var	int
	 */
	var $pointer = 0;

	/**
	 * PageId
	 *
	 * @var	int
	 */
	var $pid = 0;

	/**
	 * Startdate for filterform
	 *
	 * @var	string
	 */
	var $startDateTime = '';

	/**
	 * Enddate for filterform
	 *
	 * @var	string
	 */
	var $endDateTime = '';
	
	/**
	 * Content array for return
	 *
	 * @var	array
	 */
	var $ajaxContentArray = array();
	

	
	/**
	 * Main method			Returns list of powermails on selected page as array
	 *
	 * @return	array
	 */
	function main() {

		$this->ajaxContentArray['success'] = false;
		
		// Count numbers of all entries
		$startDateAdd = '';
		$endDateAdd = '';
		if ($this->startDateTime > 0){
			$startDateAdd = ' AND crdate > ' . $this->startDateTime;
		}

		if ($this->endDateTime > 0){
			$endDateAdd = ' AND crdate < ' . $this->endDateTime;
		}

		$select = 'count(*) AS results, MIN(crdate) AS mindate, MAX(crdate) AS maxdate';
		$from = 'tx_powermail_mails';
		$where = '
			pid = ' . intval($this->pid) .
			$startDateAdd .
			$endDateAdd . '
			AND hidden = 0
			AND deleted = 0';
		$orderBy = $this->sort . ' ' . $this->dir;
		$groupBy = '';
		$limit = '';

		// Get number of results
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		//$this->ajaxContentArray['results'] = $GLOBALS['TYPO3_DB']->sql_num_rows($res1);
		$row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1);
		$this->ajaxContentArray['results'] = $row1['results'];
		$this->ajaxContentArray['mindatetime'] = $row1['mindate'];
		$this->ajaxContentArray['maxdatetime'] = $row1['maxdate'];
		
		// Get entries
		$select = '*';
		$limit = intval($this->pointer) . ',' . intval($this->perpage);
		$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

		// If on current page is a result
		if ($res2) {
			$this->ajaxContentArray['success'] = true;
			$i = $this->pointer;
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)) {
				$i++;
				$this->ajaxContentArray['rows'][] = array(
					'id' => $i, 
					'uid' => $row['uid'], 
					'crdate' => date($this->timeformat, $row['crdate']), 
					'sender' => $row['sender'], 
					'recipient' => $row['recipient'], 
					'senderIP' => $row['senderIP'],
					'piVars' => t3lib_div::removeArrayEntryByValue(t3lib_div::xml2array($row['piVars']), '')
				);
			}
		}
		
		$GLOBALS['TYPO3_DB']->sql_free_result($res1);
		$GLOBALS['TYPO3_DB']->sql_free_result($res2);
		
		return $this->ajaxContentArray;
	}
	
	/**
	 * getLabels method			Returns labels of powermail on selected page as array
	 *
	 * @return	array
	 */
	function getLabels() {
		$labels = array();
		$i = 0;
		
		$select = 'uid,title';
		$from = 'tx_powermail_fields';
		$where = 'pid = ' . intval($this->pid);
		$orderBy = '';
		$groupBy = '';
		$limit = '';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		if ($res) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$labels['labels'][] = array(
					'uid' => $row['uid'],
					'title' => $row['title']
				);
				$i ++;
			}
			$labels['results'] = $i;
			$labels['success'] = true;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		return $labels;
	}

    /**
     * getFormTypes method			Returns form types of powermail on selected page as array
     *
     * @return	array
     */
    function getFormTypes() {
        $formtypes = array();
        $i = 0;

        $select = 'uid,formtype';
        $from = 'tx_powermail_fields';
        $where = 'pid = ' . intval($this->pid);
        $orderBy = '';
        $groupBy = '';
        $limit = '';

        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        if ($res) {
            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                $formtypes['labels'][] = array(
                    'uid' => $row['uid'],
                    'formtype' => $row['formtype']
                );
                $i ++;
            }
            $formtypes['results'] = $i;
            $formtypes['success'] = true;
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);
        return $formtypes;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_repository.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_repository.php']);
}
?>