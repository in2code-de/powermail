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
	public function main() {

		$this->ajaxContentArray['success'] = false;

		// Count numbers of all entries
		$startDateAdd = '';
		$endDateAdd = '';
		if ($this->startDateTime > 0) {
			$startDateAdd = ' AND crdate > ' . $this->startDateTime;
		}

		if ($this->endDateTime > 0) {
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
		if ($res1 !== false) {
			$row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1);
			$this->ajaxContentArray['results'] = $row1['results'];
			$this->ajaxContentArray['mindatetime'] = $row1['mindate'];
			$this->ajaxContentArray['maxdatetime'] = $row1['maxdate'];

			// Get entries
			$select = '*';
			$limit = intval($this->pointer) . ',' . intval($this->perpage);
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

			// If on current page is a result
			if ($res2 !== false) {
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
						'piVars' => $this->transformPiVars($row['piVars']),
						'uploadPath' => $row['uploadPath']
					);
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res2);
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res1);
		}
		return $this->ajaxContentArray;
	}

	/**
	 * transformPiVars method			Returns labels of powermail on selected page as array
	 *
	 * @return	array
	 */

	protected function transformPiVars($piVars) {
		if (!is_array(t3lib_div::xml2array($piVars))) return t3lib_div::xml2array($piVars);
		$piVarsArray = t3lib_div::removeArrayEntryByValue(t3lib_div::xml2array($piVars), '');
		if (array_key_exists('FILE', $piVarsArray)) {
			unset($piVarsArray['FILE']);
		}
		return $piVarsArray;
	}

	/**
	 * getLabels method			Returns labels of powermail on selected page as array
	 *
	 * @return	array
	 */
	public function getLabelsAndFormtypes() {
		$labels = array();
		$i = 0;

		$select = 'uid,title,formtype';
		$from = 'tx_powermail_fields';
		$where = 'pid = ' . intval($this->getPidOfFormFromMailsOnGivenPage($this->pid));

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);
		if ($res !== false) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$labels['labels'][] = array(
					'uid' => $row['uid'],
					'title' => $row['title'],
					'formtype' => $row['formtype']
				);
				$i++;
			}
			$labels['results'] = $i;
			$labels['success'] = true;
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}

		return $labels;
	}

	/**
	 * getPidOfFormFromMailsOnGivenPage method	 Returns the correct pid of the form from mails on given page
	 *
	 * @param  $pid	 The pid where the mails are stored
	 * @return integer  The pid where the form to this mails are stored
	 */
	protected function getPidOfFormFromMailsOnGivenPage($pid) {

		$formIdPid = $pid;

		$select = 'formid';
		$from = 'tx_powermail_mails';
		$where = 'pid = ' . intval($pid);
		$orderBy = '';
		$groupBy = '';
		$limit = '1';

		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		if ($res1 !== false) {
			$row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1);
			$formid = $row1['formid'];

			$select = 'pid';
			$from = 'tt_content';
			$where = 'uid = ' . intval($formid);
			$orderBy = '';
			$groupBy = '';
			$limit = '1';

			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
			if ($res2 !== false) {
				$row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2);
				$formIdPid = $row2['pid'];
				$GLOBALS['TYPO3_DB']->sql_free_result($res2);
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res1);
		}

		return $formIdPid;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_repository.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_repository.php']);
}
?>