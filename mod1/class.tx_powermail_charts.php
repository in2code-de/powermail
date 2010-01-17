<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Alex Kellner <alexander.kellner@einpraegsam.net>
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
require_once('class.tx_powermail_belist.php'); // include Backend list function


/**
 * Plugin 'tx_powermail_charts' for the 'powermail' extension.
 *
 * @author	Alex Kellner <alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage tx_powermail
 */
class tx_powermail_charts {

	var $tsconfig = array ( // default values for tsconfig
		'properties' => array (
			'config.' => array (
				'chart.' => array (
					'timeframe' => 2678400, // default settings for timeframe (1 month in seconds)
					'sectionframe' => 604800, // default settings for sectionframe (1 week in seconds)
					'title' => '4 Weeks ago|3 Weeks ago|2 Weeks ago|Last Week' // default settings for titles
				)
			)
		)
	);


	/**
	 * Main chart function for chart and table output
	 *
	 * @param	object		$pObj: Partent Object
	 * @return	string		$content: HTML content with table and chart
	 */
	function main($pObj) {
		// config
		$tmp_tsconfig = t3lib_BEfunc::getModTSconfig($pObj->id, 'tx_powermail_mod1'); // Get tsconfig from current page
		if (count($tmp_tsconfig['properties']['config.']['chart.']) > 0) { // if there are some values defined in tsconf
			foreach ($tmp_tsconfig['properties']['config.']['chart.'] as $key => $value) { // one loop for every chart param in tsconf
				if (!empty($value)) { // if set
					$this->tsconfig['properties']['config.']['chart.'][$key] = $value; // overwrite
				}
			}
		}
		$values = $content = '';
		$no = 0;
		$_GET['startdate'] = strftime('%Y-%m-%d %H:%M', (time() - $this->tsconfig['properties']['config.']['chart.']['timeframe'])); // overwrite GET param for start for listview
		$_GET['enddate'] = strftime('%Y-%m-%d %H:%M', time()); // overwrite GET param for end for listview
		
		// 1. get needed values
		$no_sec = floor($this->tsconfig['properties']['config.']['chart.']['timeframe'] / $this->tsconfig['properties']['config.']['chart.']['sectionframe']); // number of sections
		for ($i=0; $i < $no_sec; $i++) { // one loop for every section
			$delta1 = $this->tsconfig['properties']['config.']['chart.']['sectionframe'] * $i; // number of seconds from now to the past in current sectionframe
			$delta2 = $this->tsconfig['properties']['config.']['chart.']['timeframe'] - $delta1 - $this->tsconfig['properties']['config.']['chart.']['sectionframe']; // number of seconds inverted to $delta1
			
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // get number of mails
				'count(*) as no',
				$table = 'tx_powermail_mails',
				$where_clause = 'pid = ' . intval($pObj->id) . ' AND crdate < ' . (time() - $delta1) . ' AND crdate > ' . (time() -  $this->tsconfig['properties']['config.']['chart.']['timeframe'] + $delta2) . ' AND hidden = 0 AND deleted = 0',
				$groupBy = '',
				$orderBy = '',
				$limit = 1
			);
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // are there entries?
				'uid',
				$table,
				$where_clause,
				$groupBy,
				$orderBy,
				$limit
			);
			if ($res && $res2) {
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res); // get values
				$no += $GLOBALS['TYPO3_DB']->sql_num_rows($res2); // get numbers of entries (and add with last loop)
			}
			
			$values .= ($row['no'] > 0 ? $row['no'] : '0') . ',';
		}
		$values = substr($values, 0, -1); // delete last sign of commaseparated list
		
		if ($no > 0) { // if there are entries
			// 2. make chart link for google
			$url = 'http://chart.apis.google.com/chart?cht=lc&chd=t:' . $this->reverseList($values) . '&chs=700x200&chxt=x,y&chl=' . $this->urlencode2($this->tsconfig['properties']['config.']['chart.']['title'], '|');
			$content .= '<h2>' . $pObj->lang->getLL('chart_chart') . '</h2>';
			$content .= '<iframe style="width: 728px; height: 220px; border: 1px solid #444; padding: 5px; background-color: white;" src="';
			$content .= $url;
			$content .= '"></iframe>';
		}
			
		// 3. make listview
		if ($no > 0) $content .= '<p>&nbsp;</p>';
		if ($no > 0) $content .= '<h2>' . $pObj->lang->getLL('chart_table') . '</h2>';
		$this->belist = t3lib_div::makeInstance('tx_powermail_belist'); // list methods
		$this->belist->init($pObj->lang); // init lang
		$content .= $this->belist->main($pObj->id, $pObj->back_path, 0, 1); // show list of emails
		
		return $content;
	}
	

	/**
	 * like urlencode but splits on delimiter and than merges again (urlencode without delimiter)
	 *
	 * @param	string		$string: Overall string
	 * @param	string		$delimiter: Like , or |
	 * @return	string		$string: urlencoded string
	 */
	function urlencode2($string, $delimiter) {
		$parts = t3lib_div::trimExplode($delimiter, $string, 1);
		foreach ((array) $parts as $key => $value) { // one loop for every part
			$parts[$key] = urlencode($value); // encode current part
		}
		$string = implode($delimiter, $parts); // merge again

		return $string;
	}
	

	/**
	 * reverseList() reverses commaseparetd string (1,2,3 => 3,2,1)
	 *
	 * @param	string		$string: commaseparated string
	 * @return	string		$string: reversed string
	 */
	function reverseList($string) {
		$tmp_array = t3lib_div::trimExplode(',', $string, 1); // split on ,
		$tmp_array = array_reverse($tmp_array); // reverse array
		$string = implode(',', $tmp_array); // glue on ,
		
		return $string;
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_charts.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_charts.php']);
}
?>