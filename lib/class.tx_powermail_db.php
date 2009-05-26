<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Alexander Kellner, Mischa Heißmann <alexander.kellner@einpraegsam.net, typo3.2008@heissmann.org>
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

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('powermail').'lib/class.tx_powermail_functions_div.php'); // file for div functions

// This class saves powermail values in OTHER db tables if wanted (this class is not the main database class for storing)
class tx_powermail_db extends tslib_pibase {

	var $extKey = 'powermail';
    var $scriptRelPath = 'pi1/class.tx_powermail_pi1.php'; // Path to pi1 to get locallang.xml from pi1 folder
	var $dbInsert = 1; // Disable db insert for testing only
	
	
	// Main Function for inserting datas to other tables
	function main($conf, $sessiondata, $cObj, $ok) {
		// config
		$this->cObj = $cObj; // cObject
		$this->conf = $conf; // conf
		$this->sessiondata = $sessiondata; // sessionvalues
		$this->div = t3lib_div::makeInstance('tx_powermail_functions_div'); // Create new instance for div class
		$this->db_values = $this->db_values_mm = $this->debug_array = array(); // init dbArray
		
		// let's go
		if ($ok) { // if it's allowed to save db values
		
			if (isset($this->conf['dbEntry.']) && is_array($this->conf['dbEntry.'])) { // Only if any dbEntry is set per typoscript
				foreach ($this->conf['dbEntry.'] as $key => $value) { // One loop for every table to insert
					
					if ($this->cObj->cObjGetSingle($this->conf['dbEntry.'][$key]['_enable'], $this->conf['dbEntry.'][$key]['_enable.']) == 1) { // only if db entry is allowed for current table (dbEntry.tt_address._enable.value = 1)
						
						// 1. Array for first db entry
						if (isset($this->conf['dbEntry.'][$key]) && is_array($this->conf['dbEntry.'][$key])) { // Only if its an array
							
							foreach ($this->conf['dbEntry.'][$key] as $kk => $vv) { // One loop for every field to insert in current table
								if (substr($kk, 0, 1) != '_' && substr($kk, -1) != '.') { // if fieldname is not _enable or _mm and not with . at the end
									
									if ($this->fieldExists($kk, str_replace('.','',$key))) { // if db table and field exists
										$this->db_values[$key][$kk] = $this->cObj->cObjGetSingle($this->conf['dbEntry.'][$key][$kk], $this->conf['dbEntry.'][$key][$kk.'.']); // write current TS value to array
									}
									
								}
							}
							
						}
					
						// 2. DB insert
						if (count($this->db_values[$key]) > 0) { // if db array is not empty
							
							// 2.1 Main db insert for main table
							$this->dbUpdate(str_replace('.', '', $key), $this->db_values[$key]); // DB entry for every table
							
							
							// 2.1 db entry for mm tables if set
							if (count($this->conf['dbEntry.'][$key]['_mm.']) > 0) { // if mm entry enabled
								foreach ($this->conf['dbEntry.'][$key]['_mm.'] as $kkk => $vvv) { // One loop for every mm db insert
									if (substr($kkk, -1) == '.') { // We want the array
										if (
											$this->fieldExists('uid_local', $this->cObj->cObjGetSingle($this->conf['dbEntry.'][$key]['_mm.'][$kkk]['1'], $this->conf['dbEntry.'][$key]['_mm.'][$kkk]['1.']))
											&&
											$this->fieldExists('uid', $this->cObj->cObjGetSingle($this->conf['dbEntry.'][$key]['_mm.'][$kkk]['2'], $this->conf['dbEntry.'][$key]['_mm.'][$kkk]['2.']))
											&&
											is_numeric($this->cObj->cObjGetSingle($this->conf['dbEntry.'][$key]['_mm.'][$kkk]['3'], $this->conf['dbEntry.'][$key]['_mm.'][$kkk]['3.']))
										) { // 1. is db table && 2. is db table && 3. is a number
											if ($this->uid[str_replace('.', '', $key)] > 0) { // if uid_local exists
												$this->db_values_mm[$key] = array (
													'uid_local' => $this->uid[str_replace('.', '', $key)],
													'uid_foreign' => $this->cObj->cObjGetSingle($this->conf['dbEntry.'][$key]['_mm.'][$kkk]['3'], $this->conf['dbEntry.'][$key]['_mm.'][$kkk]['3.'])
												);
											}
											if (count($this->db_values_mm[$key]) > 0) $this->dbUpdate($this->cObj->cObjGetSingle($this->conf['dbEntry.'][$key]['_mm.'][$kkk]['1'], $this->conf['dbEntry.'][$key]['_mm.'][$kkk]['1.']), $this->db_values_mm[$key]); // DB entry for every table
										}
									}
								}
							}
						}
					}		
				}
			}
			$this->debug(); // 3. Debug output
		}
	}
	
	
	// Function dbUpdate() inserts or updates database
	function dbUpdate($table, $values) {
	
		if (count($values) > 0) { // if there are values
			if (!isset($this->conf['dbEntry.'][$table.'.']['_ifUnique.']) || $this->conf['dbEntry.'][$table.'.']['_ifUnique.'] == 'disable') { // no unique values
			
				if ($this->dbInsert) { // if allowed
					$GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $values); // DB entry for every table
					$this->uid[$table] = mysql_insert_id(); // Get uid of current db entry
				}
				
			} else { // unique values
			
				$uniqueField = key($this->conf['dbEntry.'][$table.'.']['_ifUnique.']); // get first entry of this array
				$mode = $this->conf['dbEntry.'][$table.'.']['_ifUnique.'][$uniqueField]; // mode could be "none" or "update"
				if ($this->fieldExists('uid', $table)) { // check if field uid exists in table
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // get uid of existing value
						'uid',
						$table,
						$where_clause = $uniqueField . ' = "'.$this->cObj->cObjGetSingle($this->conf['dbEntry.'][$table.'.'][$uniqueField], $this->conf['dbEntry.'][$table.'.'][$uniqueField.'.']).'"' . ($this->fieldExists('deleted', $table) ? ' AND deleted = 0' : ''),
						$groupBy = '',
						$orderBy = '',
						$limit = 1
					);
					if ($res) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				}
				
				if ($row['uid'] > 0) { // there is already an entry in the database
					switch ($mode) {
						case 'update': // mode is update
							$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, 'uid = '.$row['uid'], $values); // update old entry with new values
							//$this->uid[$table] = $row['uid']; // make row uid global
							break;
							
						case 'none': // mode is none
						default:
							// do nothing
							$this->db_values = 'Entry already exists, won\'t be overwritten';
							break;
					}
								
				} else { // there is no entry in the database
					
					$GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $values); // New DB entry
					$this->uid[$table] = mysql_insert_id(); // Get uid of current db entry
					
				}
				
			}
		}
		
	}
	
	
	// Function fieldExists() checks if a table and field exist in mysql db
	function fieldExists($field = '', $table = '') {
		if (!empty($field) && !empty($table) && strpos($field, ".") === false) {
			// check if table and field exits in db
			$row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( mysql_query('SHOW TABLES LIKE "'.$table.'"') ); // check if table exist
			if ($row1) $row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( mysql_query('DESCRIBE '.$table.' '.$field) ); // check if field exist (if table is wront - errormessage)
			
			// debug values
			if (!$row1) $this->debug_array['ERROR'][] = 'Table "'.$table.'" don\'t exists in db'; // errormessage if table don't exits
			if (!$row2 && $row1) $this->debug_array['ERROR'][] = 'Field "'.$field.'" don\'t exists in db table "'.$table.'"'; // errormessage if field don't exits
			
			// return true or false
			if ($row1 && $row2) return 1; // table and field exist
			else return 0; // table or field don't exist
		}
	}
	
	
	// Function debug() generates debug output
	function debug() {
		$this->debug_array['Main Table'] = $this->db_values; // array for debug view
		$this->debug_array['MM Table'] = (count($this->db_values_mm) > 0 ? $this->db_values_mm : 'no values or entry already exists'); // array for debug view
		if ($this->conf['debug.']['output'] == 'all' || $this->conf['debug.']['output'] == 'externdbtable') $this->div->debug($this->debug_array, 'Extern DB-table entries'); // Debug function
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_db.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_db.php']);
}

?>