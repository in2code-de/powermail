<?php
namespace In2code\Powermail\Utility;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * This class allows you to save values to any table in TYPO3 database
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class SaveToAnyTable {

	/**
	 * Database Table to store
	 *
	 * @var string
	 */
	protected $table = '';

	/**
	 * Array with fieldname=>value
	 *
	 * @var array
	 */
	protected $properties = array();

	/**
	 * Mode "insert", "update"
	 *
	 * @var string
	 */
	protected $mode = 'insert';

	/**
	 * Unique field important for update
	 *
	 * @var string
	 */
	protected $uniqueField = 'uid';

	/**
	 * Switch on devLog
	 *
	 * @var bool
	 */
	protected $devLog = FALSE;

	/**
	 * Executes the storage
	 *
	 * @return int uid of inserted record
	 */
	public function execute() {
		switch ($this->getMode()) {
			case 'update':
				// case with "update" or "none"
			case 'none':
				$uid = $this->update();
				break;

			case 'insert':
			default:
				$uid = $this->insert();
		}
		$this->writeToDevLog();
		return $uid;
	}

	/**
	 * Insert new record
	 *
	 * @return \int uid of inserted record
	 */
	protected function insert() {
		$GLOBALS['TYPO3_DB']->exec_INSERTquery($this->getTable(), $this->getProperties());
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}

	/**
	 * Update existing record
	 *
	 * @return \int uid of updated record
	 */
	protected function update() {
		// find existing record in database
		$searchterm = $GLOBALS['TYPO3_DB']->fullQuoteStr(
			$this->getProperty(
				$this->getUniqueField()
			),
			$this->getTable()
		);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			$this->getTable(),
			$this->getUniqueField() . ' = ' . $searchterm . ' and deleted = 0',
			'',
			'',
			1
		);
		if ($res) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}

		// if there is no existing entry, insert new one
		if (empty($row['uid'])) {
			return $this->insert();
		}

		// update existing entry (only if mode is not "none")
		if ($this->getMode() !== 'none') {
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				$this->getTable(),
				'uid = ' . intval($row['uid']),
				$this->getProperties()
			);
		}

		return $row['uid'];
	}

	/**
	 * Set TableName
	 *
	 * @param string $table
	 * @return void
	 */
	public function setTable($table) {
		$this->removeNotAllowedSigns($table);
		$this->table = $table;
	}

	/**
	 * Get TableName
	 *
	 * @return string
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * Read properties
	 *
	 * @return array
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * Get one property value
	 *
	 * @param $propertyName
	 * @return \string
	 */
	public function getProperty($propertyName) {
		$property = '';
		$properties = $this->getProperties();
		if (array_key_exists($propertyName, $properties)) {
			$property = $properties[$propertyName];
		}
		return $property;
	}

	/**
	 * Add property/value pair to array
	 *
	 * @param $propertyName
	 * @param $value
	 * @return void
	 */
	public function addProperty($propertyName, $value) {
		$this->removeNotAllowedSigns($propertyName);
		$this->properties[$propertyName] = $value;
	}

	/**
	 * Remove property/value pair form array by its key
	 *
	 * @param $propertyName
	 * @return void
	 */
	public function removeProperty($propertyName) {
		unset($this->properties[$propertyName]);
	}

	/**
	 * @param string $mode
	 * @return void
	 */
	public function setMode($mode) {
		$possibleModes = array(
			'insert',
			'update',
			'none'
		);
		if (in_array($mode, $possibleModes)) {
			$this->mode = $mode;
		}
	}

	/**
	 * @return string
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * @param string $uniqueField
	 * @return void
	 */
	public function setUniqueField($uniqueField) {
		$this->uniqueField = $uniqueField;
	}

	/**
	 * @return string
	 */
	public function getUniqueField() {
		return $this->uniqueField;
	}

	/**
	 * @param boolean $devLog
	 * @return void
	 */
	public function setDevLog($devLog) {
		$this->devLog = $devLog;
	}

	/**
	 * @return boolean
	 */
	public function getDevLog() {
		return $this->devLog;
	}

	/**
	 * Remove not allowed signs
	 *
	 * @param $string
	 * @return void
	 */
	protected function removeNotAllowedSigns(&$string) {
		$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
	}

	/**
	 * Write settings to devlog
	 *
	 * @return void
	 */
	protected function writeToDevLog() {
		if (!$this->getDevLog()) {
			return;
		}
		$subject = 'SaveToAnyTable (Table: ' . $this->getTable();
		$subject .= ', Mode: ' . $this->getMode();
		$subject .=  ', UniqueField: ' . $this->getUniqueField() . ')';
		GeneralUtility::devLog(
			$subject,
			'powermail',
			0,
			$this->getProperties()
		);
	}

}