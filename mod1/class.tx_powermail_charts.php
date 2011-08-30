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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   50: class tx_powermail_charts
 *   84:	 function main($pObj)
 *  172:	 function urlencode2($string, $delimiter)
 *  188:	 function reverseList($string)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_functions_div.php');
require_once(t3lib_extMgm::extPath('powermail') . 'mod1/class.tx_powermail_belist.php');

/**
 * Plugin 'tx_powermail_charts' for the 'powermail' extension.
 *
 * @author	powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
 * @package	TYPO3
 * @subpackage tx_powermail
 */
class tx_powermail_charts {

	/**
	 * Default values for pageTSConfig
	 *
	 * @var	array
	 */
	var $tsconfig = array(
		'properties' => array(
			'config.' => array(
				'chart.' => array(
					// Default settings for timeframe (1 month in seconds)
					'timeframe' => 2678400,
					// Default settings for sectionframe (1 week in seconds)
					'sectionframe' => 604800,
					'title' => '4 Weeks ago|3 Weeks ago|2 Weeks ago|Last Week'
				)
			)
		)
	);

	/**
	 * BeList object
	 *
	 * @var	tx_powermail_belist
	 */
	var $belist = null;

	/**
	 * Main chart function for chart and table output
	 *
	 * @param	object		$pObj: Partent Object
	 * @return	string		$content: HTML content with table and chart
	 */
	function main($pObj) {
		$tmp_tsconfig = t3lib_BEfunc::getModTSconfig($pObj->id, 'tx_powermail_mod1');

		// If there are some values defined in tsconf
		if (count($tmp_tsconfig['properties']['config.']['chart.']) > 0) {
			foreach ($tmp_tsconfig['properties']['config.']['chart.'] as $key => $value) {
				if (!empty($value)) {
					$this->tsconfig['properties']['config.']['chart.'][$key] = $value;
				}
			}
		}

		$this->tsconfig['properties']['config.']['chart.']['timeframe'] = intval($this->tsconfig['properties']['config.']['chart.']['timeframe']);
		$this->tsconfig['properties']['config.']['chart.']['sectionframe'] = intval($this->tsconfig['properties']['config.']['chart.']['sectionframe']);

		$values = $content = '';
		$no = 0;

		// Overwrite GET param for start for listview
		$_GET['startdate'] = strftime('%Y-%m-%d %H:%M', (time() - $this->tsconfig['properties']['config.']['chart.']['timeframe']));
		$_GET['enddate'] = strftime('%Y-%m-%d %H:%M', time());

		// Number of sections
		$no_sec = floor($this->tsconfig['properties']['config.']['chart.']['timeframe'] / $this->tsconfig['properties']['config.']['chart.']['sectionframe']);
		for ($i = 0; $i < $no_sec; $i++) {
			// Number of seconds from now to the past in current sectionframe
			$delta1 = $this->tsconfig['properties']['config.']['chart.']['sectionframe'] * $i;

			// Number of seconds inverted to $delta1
			$delta2 = $this->tsconfig['properties']['config.']['chart.']['timeframe'] - $delta1 - $this->tsconfig['properties']['config.']['chart.']['sectionframe'];

			// Get number of mails
			$select = 'count(*) as no';
			$from = 'tx_powermail_mails';
			$where = '
				pid = ' . intval($pObj->id) . '
				AND crdate < ' . (time() - $delta1) . '
				AND crdate > ' . (time() - $this->tsconfig['properties']['config.']['chart.']['timeframe'] + $delta2) . '
				AND hidden = 0
				AND deleted = 0';
			$groupBy = $orderBy = '';
			$limit = 1;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

			$select = 'uid';
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

			if ($res && $res2) {
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$no += $GLOBALS['TYPO3_DB']->sql_num_rows($res2);
			}

			if ($row['no'] > 0) {
				$values .= $row['no'];

			} else {
				$values .= '0';
			}
			$values .= ',';
		}
		$values = substr($values, 0, -1);

		// If there are entries: make chart link for google
		if ($no > 0) {
			$url = 'http://chart.apis.google.com/chart?cht=lc&chd=t:' . $this->reverseList($values) . '&chs=700x200&chxt=x,y&chl=' . $this->urlencode2($this->tsconfig['properties']['config.']['chart.']['title'], '|');
			$content .= '
				<h2>' . $pObj->lang->getLL('chart_chart') . '</h2>
				<iframe style="width: 728px; height: 220px; border: 1px solid #444; padding: 5px; background-color: white;" src="' . $url . '"></iframe>';
		}

		// Make listview
		if ($no > 0) {
			$content .= '
				<p>&nbsp;</p>
				<h2>' . $pObj->lang->getLL('chart_table') . '</h2>';
		}
		$this->belist = t3lib_div::makeInstance('tx_powermail_belist');
		$this->belist->init($pObj->lang);

		$content .= $this->belist->main($pObj->id, $pObj->back_path, 0, 1);

		return $content;
	}

	/**
	 * Like urlencode but splits on delimiter and than merges again (urlencode without delimiter)
	 *
	 * @param	string		$string: Overall string
	 * @param	string		$delimiter: Like , or |
	 * @return	string		$string: urlencoded string
	 */
	function urlencode2($string, $delimiter) {
		$parts = t3lib_div::trimExplode($delimiter, $string, 1);
		foreach ((array)$parts as $key => $value) {
			$parts[$key] = rawurlencode($value);
		}
		$string = implode($delimiter, $parts);

		return $string;
	}

	/**
	 * reverseList() reverses commaseparetd string (1,2,3 => 3,2,1)
	 *
	 * @param	string		$string: commaseparated string
	 * @return	string		$string: reversed string
	 */
	function reverseList($string) {
		$tmp_array = t3lib_div::trimExplode(',', $string, 1);
		$tmp_array = array_reverse($tmp_array);
		$string = implode(',', $tmp_array);

		return $string;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_charts.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_charts.php']);
}
?>