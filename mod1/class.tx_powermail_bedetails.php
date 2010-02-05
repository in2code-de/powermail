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

class tx_powermail_bedetails {

	/**
	 * $LANG object
	 *
	 * @var language
	 */
	var $LANG = null;
	
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
	 * Main method of bedetails class
	 * Method to show the details of a single mail
	 *
	 * @param int $mailID
	 * @param lang $LANG
	 * @return string
	 */
	function main($mailID, $LANG) {
		global $BACK_PATH;
		
		$this->mailID = $mailID;
		$this->LANG = $LANG;
		$this->backpath = $BACK_PATH;
		
		// Building "src" parameter for close image
		$imgSrc = $this->backpath . 'gfx/closedok.gif';
		$alternativCloseSrc = $this->backpath . 'sysext/t3skin/icons/gfx/closedok.gif';
		if(is_file($alternativCloseSrc)) {
			$imgSrc = $alternativCloseSrc;
		}
		
		$this->content = '
			<br />
			<hr />
			<div style="padding-bottom: 5px;">
				<a href="#" onclick="window.close();">
					<img src="' . $imgSrc . '" title="' . $this->LANG->getLL('icon_close') . '" alt="Close" style="margin: 6px;" />
				</a>
				<a href="#" onclick="window.print();">
					<img src="../img/icon_print.gif" style="margin: 5px;" title="' . $this->LANG->getLL('icon_print') . '" alt="Print" />
				</a
			</div>
			<table>';
		
		$select = 'piVars';
		$from = 'tx_powermail_mails';
		$where = 'hidden = 0 AND deleted = 0 AND uid = ' . intval($this->mailID);
		$groupBy = $orderBy = $limit = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		if (is_array($row)) {
			
			$values = t3lib_div::xml2array($row['piVars'], 'pivars');
			if (!is_array($values)){
				$values = t3lib_div::xml2array(utf8_encode($row['piVars']), 'pivars');
			} elseif ($this->LANG->charSet != 'utf-8') {
				$values = t3lib_div::xml2array(utf8_decode($row['piVars']), 'pivars');
			}
			
			if (isset($values) && is_array($values)) {
				foreach ($values as $key => $value) { // one loop for every piVar
					if (!is_array($value)) { // if value is not an array (first level)
						$this->content .= '
							<tr>
								<td><strong>' . $this->GetLabelfromBackend($key, $value) . ':</strong></td>
								<td style="padding-left: 10px;">' . $value . '</td>
								<td style="padding-left: 10px; color: #aaa;">(' . $key . ')</td>
							</tr>';
						
					} else { // is array (second level)
						foreach ($values[$key] as $key2 => $value2) { // one loop for every piVar in second level
							$this->content .= '
								<tr>
									<td><strong>' . $this->GetLabelfromBackend($key, $value) . ':</strong></td>
									<td style="padding-left: 10px;">' . $value2 . '</td>
									<td style="padding-left: 10px; color: #aaa;">(' . $key . '_' . $key2 . ')</td>
								</tr>';
						}
					}
				}
			}
		}

		$this->content .= '</table>';

		return $this->content;
	}
    
    /**
     * Method GetLabelfromBackend() to get label to current field for emails and thx message
     *
     * @param string $name
     * @param string $value
     * @return string
     */
    function GetLabelfromBackend($name, $value) {
    	$labelToReturn = $name;
    	
		if (strpos($name, 'uid') !== FALSE) { // $name like uid55
			$uid = str_replace('uid', '', $name);
			
			$select = 'f.title';
			$from = '
				tx_powermail_fields f 
				LEFT JOIN tx_powermail_fieldsets fs 
				ON (
					f.fieldset = fs.uid
				) 
				LEFT JOIN tt_content c 
				ON (
					c.uid = fs.tt_content
				)';
			$where = '
				c.deleted=0 
				AND c.hidden=0 
				AND (c.starttime<=' . time() . ') 
				AND (c.endtime=0 OR c.endtime>' . time() . ') 
				AND (
					c.fe_group="" 
					OR c.fe_group IS NULL 
					OR c.fe_group="0" 
					OR (
						c.fe_group LIKE "%,0,%" 
						OR c.fe_group LIKE "0,%" 
						OR c.fe_group LIKE "%,0" 
						OR c.fe_group="0"
					) 
					OR (
						c.fe_group LIKE "%,-1,%" 
						OR c.fe_group LIKE "-1,%" 
						OR c.fe_group LIKE "%,-1" 
						OR c.fe_group="-1"
					)
				)
				AND f.uid = ' . intval($uid) . ' 
				AND f.hidden = 0 
				AND f.deleted = 0';
			$groupBy = $orderBy = $limit = '';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			
			$labelToReturn = '[NO TITLE]';
			if (isset($row['title'])){
				$labelToReturn = $row['title'];
			}
		}
		
		return $labelToReturn;
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_bedetails.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_bedetails.php']);
}
?>