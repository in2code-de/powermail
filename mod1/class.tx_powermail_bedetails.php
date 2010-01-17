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

	// Function Main
	function main($mailID, $LANG) {
		// config
		global $BACK_PATH;
		$this->mailID = $mailID;
		$this->LANG = $LANG;
		$this->backpath = $BACK_PATH;
		
		// let's go
		$this->content = '';
		$this->content .= '<br /><hr />';
		$this->content .= '<div style="padding-bottom: 5px;">';
		$this->content .= '<a href="#" onclick="window.close();"><img src="' . (is_file($this->backpath . 'sysext/t3skin/icons/gfx/closedok.gif') ? $this->backpath . 'sysext/t3skin/icons/gfx/closedok.gif' : $this->backpath . 'gfx/closedok.gif') . '" title="' . $this->LANG->getLL('icon_close') . '" alt="Close" style="margin: 6px;" /></a>'; // close icon
		$this->content .= '<a href="#" onclick="window.print();"><img src="../img/icon_print.gif" style="margin: 5px;" title="' . $this->LANG->getLL('icon_print') . '" alt="Print" /></a>'; // print icon
		$this->content .= '</div>';
		$this->content .= '<table>';
		
		// db query
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'piVars',
			'tx_powermail_mails',
			$where_clause = 'hidden = 0 AND deleted = 0 AND uid = ' . intval($this->mailID),
			$groupBy = '',
			$orderBy = '',
			$limit = ''
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (isset($row)) {
			$values = t3lib_div::xml2array($row['piVars'], 'pivars'); // xml2array
			if (!is_array($values)) $values = t3lib_div::xml2array(utf8_encode($row['piVars']), 'pivars'); // xml2array
			elseif ($this->LANG->charSet != 'utf-8') $values = t3lib_div::xml2array(utf8_decode($row['piVars']), 'pivars'); // xml2array
			
			if (isset($values) && is_array($values)) {
				foreach ($values as $key => $value) { // one loop for every piVar
					if (!is_array($value)) { // if value is not an array (first level)
						$this->content .= '<tr>'; // open row
						$this->content .= '<td><strong>' . $this->GetLabelfromBackend($key, $value) . ':</strong></td>'; // first cell with label
						$this->content .= '<td style="padding-left: 10px;">' . $value . '</td>'; // second cell with value
						$this->content .= '<td style="padding-left: 10px; color: #aaa;">(' . $key . ')</td>'; // third cell with uid
						$this->content .= '</tr>'; // close row
					} else { // is array (second level)
						foreach ($values[$key] as $key2 => $value2) { // one loop for every piVar in second level
							$this->content .= '<tr>'; // open row
							$this->content .= '<td><strong>' . $this->GetLabelfromBackend($key, $value) . ':</strong></td>'; // first cell with label
							$this->content .= '<td style="padding-left: 10px;">' . $value2 . '</td>'; // second cell with value
							$this->content .= '<td style="padding-left: 10px; color: #aaa;">(' . $key . '_' . $key2 . ')</td>'; // third cell with uid
							$this->content .= '</tr>'; // close row
						}
					}
				}
			}
		}
		$this->content .= '</table>';
		
		
		return $this->content; // return
	}
    
    // Function GetLabelfromBackend() to get label to current field for emails and thx message
    function GetLabelfromBackend($name, $value) {
		if (strpos($name, 'uid') !== FALSE) { // $name like uid55
			$uid = str_replace('uid', '', $name);

			$where_clause = 'c.deleted=0 AND c.hidden=0 AND (c.starttime<=' . time() . ') AND (c.endtime=0 OR c.endtime>' . time() . ') AND (c.fe_group="" OR c.fe_group IS NULL OR c.fe_group="0" OR (c.fe_group LIKE "%,0,%" OR c.fe_group LIKE "0,%" OR c.fe_group LIKE "%,0" OR c.fe_group="0") OR (c.fe_group LIKE "%,-1,%" OR c.fe_group LIKE "-1,%" OR c.fe_group LIKE "%,-1" OR c.fe_group="-1"))'; // enable fields for tt_content
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // GET title where fields.flexform LIKE <value index="vDEF">vorname</value>
				'f.title',
				'tx_powermail_fields f LEFT JOIN tx_powermail_fieldsets fs ON f.fieldset = fs.uid LEFT JOIN tt_content c ON c.uid = fs.tt_content',
				$where_clause .= ' AND f.uid = ' . intval($uid) . ' AND f.hidden = 0 AND f.deleted = 0',
				$groupBy = '',
				$orderBy = '',
				$limit = ''
			);
			if ($res) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			
			if (isset($row['title'])) return $row['title']; // if title was found return ist
			else return '[NO TITLE]'; // if no title was found return 
		} else { // no uid55 so return $name
			return $name;
		}
    }
	
	// Init
	function init($LANG) {
		$this->LANG = $LANG; // make $LANG global
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_bedetails.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_bedetails.php']);
}
?>