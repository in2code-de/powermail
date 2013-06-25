<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * This class allows you to save values to any table
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class Tx_Powermail_Utility_SaveToTable {

	/**
	 * cObj
	 *
	 * @var Content Object
	 */
	protected $cObj;

	/**
	 * TypoScript configuration
	 *
	 * @var configuration
	 */
	protected $conf;

	/**
	 * All Plugin Params
	 *
	 * @var array
	 */
	protected $allArguments;

	/**
	 * Debug Array
	 *
	 * @var array
	 */
	protected $debug_array;

	/**
	 * Values to store
	 *
	 * @var array
	 */
	protected $db_values;

	/**
	 * Stop db insert for testing
	 *
	 * @var boolean
	 */
	protected $dbInsert = 1;

	/**
	 * Preflight method to store values to any db table
	 */
	public function main($allArguments, $conf, $cObj, $ok = 1) {
		$this->cObj = $cObj; // cObject
		$this->conf = $conf; // conf
		$this->allArguments = $allArguments; // sessionvalues

		if (!$ok || !isset($this->conf['dbEntry.']) || !is_array($this->conf['dbEntry.'])) {
			return;
		}

 		// One loop for every table to insert
		foreach ($this->conf['dbEntry.'] as $table => $value) {

			if ($this->cObj->cObjGetSingle($this->conf['dbEntry.'][$table]['_enable'], $this->conf['dbEntry.'][$table]['_enable.']) != 1) {
				continue;
			}

			// 1. Array for first db entry
			foreach ((array) $this->conf['dbEntry.'][$table] as $field => $value2) { // One loop for every field to insert in current table
				if ($field[0] == '_' || substr($field, -1) == '.') { // if fieldname is _enable or _mm and not with . at the end
					continue; // go to next loop
				}

				if ($this->fieldExists($field, $this->removeDot($table))) { // if db table and field exists
					$this->cObj->start($allArguments); // push to ts
					$this->db_values[$table][$field] = $this->cObj->cObjGetSingle($this->conf['dbEntry.'][$table][$field], $this->conf['dbEntry.'][$table][$field . '.']); // write current TS value to array
				}
			}

			// 2. DB insert
			$this->dbUpdate($this->removeDot($table), $this->db_values[$table]); // Main DB entry for every table

			// 2.1 db entry for mm tables if set
			if (count($this->conf['dbEntry.'][$table]['_mm.']) > 0) { // if mm entry enabled
				foreach ($this->conf['dbEntry.'][$table]['_mm.'] as $key_mm => $value_mm) { // One loop for every mm db insert
					if (substr($key_mm, -1) == '.') { // We want the array
						if (
							$this->fieldExists('uid_local', $this->cObj->cObjGetSingle($this->conf['dbEntry.'][$table]['_mm.'][$key_mm]['1'], $this->conf['dbEntry.'][$table]['_mm.'][$key_mm]['1.']))
							&&
							$this->fieldExists('uid', $this->cObj->cObjGetSingle($this->conf['dbEntry.'][$table]['_mm.'][$key_mm]['2'], $this->conf['dbEntry.'][$table]['_mm.'][$key_mm]['2.']))
							&&
							is_numeric($this->cObj->cObjGetSingle($this->conf['dbEntry.'][$table]['_mm.'][$key_mm]['3'], $this->conf['dbEntry.'][$table]['_mm.'][$key_mm]['3.']))
						) { // 1. is db table && 2. is db table && 3. is a number
							if ($this->uid[str_replace('.', '', $table)] > 0) { // if uid_local exists
								$this->db_values_mm[$table] = array (
									'uid_local' => $this->uid[str_replace('.', '', $table)],
									'uid_foreign' => $this->cObj->cObjGetSingle($this->conf['dbEntry.'][$table]['_mm.'][$key_mm]['3'], $this->conf['dbEntry.'][$table]['_mm.'][$key_mm]['3.'])
								);
							}
							if (count($this->db_values_mm[$table]) > 0) { // DB entry for every table
								$this->dbUpdate($this->cObj->cObjGetSingle($this->conf['dbEntry.'][$table]['_mm.'][$key_mm]['1'], $this->conf['dbEntry.'][$table]['_mm.'][$key_mm]['1.']), $this->db_values_mm[$table]);
							}
						}
					}
				}
			}
		}

		$this->debug();
	}

	/**
	 * Function dbUpdate() inserts or updates database
	 *
	 * @param	string		Table
	 * @param	array		values
	 * @return	void
	 */
	protected function dbUpdate($table, $values) {
		if (count($values) == 0) { // if there are values
			return;
		}
		if (!isset($this->conf['dbEntry.'][$table . '.']['_ifUnique.']) || $this->conf['dbEntry.'][$table . '.']['_ifUnique.'] == 'disable') { // no unique values
			if ($this->dbInsert) { // if allowed
				$GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $values); // DB entry for every table
				$this->uid[$table] = $GLOBALS['TYPO3_DB']->sql_insert_id(); // Get uid of current db entry
			}

		} else { // unique values

			$uniqueField = key($this->conf['dbEntry.'][$table . '.']['_ifUnique.']); // get first entry of this array
			$mode = $this->conf['dbEntry.'][$table . '.']['_ifUnique.'][$uniqueField]; // mode could be "none" or "update"
			if ($this->fieldExists('uid', $table)) { // check if field uid exists in table
				// get uid of existing value
				$select = 'uid';
				$from = $table;
				$where = $uniqueField;
				$where .= ' = "' . $this->cObj->cObjGetSingle($this->conf['dbEntry.'][$table . '.'][$uniqueField], $this->conf['dbEntry.'][$table . '.'][$uniqueField . '.']) . '"';
				$where .= ($this->fieldExists('deleted', $table) ? ' AND deleted = 0' : '');
				$groupBy = '';
				$orderBy = '';
				$limit = 1;
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
				if ($res) {
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				}
			}

			if ($row['uid'] > 0) { // there is already an entry in the database
				switch ($mode) {
					case 'update': // mode is update
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, 'uid = ' . intval($row['uid']), $values); // update old entry with new values
							// Make row uid global
						$this->uid[$table] = $row['uid'];
						break;

					case 'none': // mode is none
					default:
						// do nothing
						$this->db_values = 'Entry already exists, won\'t be overwritten';
						break;
				}

			} else { // there is no entry in the database

				$GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $values); // New DB entry
				$this->uid[$table] = $GLOBALS['TYPO3_DB']->sql_insert_id(); // Get uid of current db entry

			}

		}
	}

	/**
	 * Function fieldExists() checks if a table and field exist in mysql db
	 *
	 * @param	string		field
	 * @param	string		table
	 * @return	void
	 */
	protected function fieldExists($field = '', $table = '') {
		if (empty($field) || empty($table) || stristr($field, '.')) {
			return 0;
		}

		// check if table and field exits in db
		$allTables = $GLOBALS['TYPO3_DB']->admin_get_tables();
		$tableInfo = $allTables[$table];
		if (is_array($tableInfo)) {
			$allFields = $GLOBALS['TYPO3_DB']->admin_get_fields($table); // check if field exist (if table is wront - errormessage)
			$fieldInfo = $allFields[$field];
		}

		// debug values
		if (!is_array($tableInfo)) {
			$this->debug_array['ERROR'][] = 'Table "' . $table . '" don\'t exists in db'; // errormessage if table don't exits
		}
		if (is_array($tableInfo) && !is_array($fieldInfo)) {
			$this->debug_array['ERROR'][] = 'Field "' . $field . '" don\'t exists in db table "' . $table . '"'; // errormessage if field don't exits
		}

		// return true or false
		if (is_array($tableInfo) && is_array($fieldInfo)) {
			return 1; // table and field exist
		} else {
			return 0; // table or field don't exist
		}
	}

	/**
	 * Remove . from string
	 *
	 * @param	string	string with a .
	 * @return	string	string without any .
	 */
	protected function removeDot($string) {
		return str_replace('.', '', $string);
	}

	/**
	 * Pushes out some debug messages
	 *
	 * @return void
	 */
	protected function debug() {
		// Debug Output
		$this->debug_array['Main Table'] = $this->db_values; // array for debug view
		$this->debug_array['MM Table'] = (count($this->db_values_mm) > 0 ? $this->db_values_mm : 'no values or entry already exists'); // array for debug view
		if ($this->conf['debug.']['saveToTable']) {
			t3lib_utility_Debug::debug($this->debug_array, 'powermail debug: Show Values from "SaveToTable" Function');
		}
	}
}

?>