<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
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


if (!defined('PATH_typo3conf')) die ('Could not access this script directly!');

require_once(PATH_tslib . 'class.tslib_pibase.php');

class tx_powermail_countryzones extends tslib_pibase {

	public function main($data) {
		//$value = 'DE';
		$value = $data['iso2'];
		$content = array();
		$content[] = array('id' => intval(str_replace('uid', '', $data['uid'])));
		tslib_eidtools::connectDB(); //Connect to database

		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tt_content.uid',
			'tx_powermail_fields LEFT JOIN tt_content ON tx_powermail_fields.pid = tt_content.pid',
			'tx_powermail_fields.uid = ' . $content[0]['id']
		);

		if ($res1 !== false) {
			$row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1);
			$TSFE = tslib_eidtools::initFeUser();
			$sessionvars = $TSFE->getKey('ses', 'powermail_' . $row1['uid']);
			$content[0]['selected'] = $sessionvars['uid' . (100000 + $content[0]['id'])];
			$GLOBALS['TYPO3_DB']->sql_free_result($res1);
		}

		$where_clause = 'static_countries.cn_iso_2 = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($value);
		$where_clause .= ' OR static_countries.cn_iso_3 = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($value);
		$where_clause .= ' OR static_countries.cn_short_en = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($value); // where clause if value of country given

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'zn_code, zn_name_local',
			'static_countries LEFT JOIN static_country_zones ON static_countries.cn_iso_2 = static_country_zones.zn_country_iso_2',
			$where_clause,
			$groupBy = '',
			$orderBy = 'static_country_zones.zn_name_local',
			$limit = ''
		);

		if ($res !== false) { // If there is a result
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every country_zone
				$content[] = array('zn_code' => $row['zn_code'], 'zn_name_local' => $row['zn_name_local']);
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			if ($content[1]['zn_code'] == null) {
				$content[0]['selected'] = '';
				unset($sessionvars['uid' . (10000 + $content[0]['id'])]);
				$TSFE->setKey('ses', 'powermail_' . $row1['uid'], $sessionvars);
				$TSFE->storeSessionData();
			}
		}

		return $content;
	}
}

$output = t3lib_div::makeInstance('tx_powermail_countryzones');
$response = $output->main(t3lib_div::_GET());
echo json_encode($response);

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_countryzones.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_countryzones.php']);
}

?>