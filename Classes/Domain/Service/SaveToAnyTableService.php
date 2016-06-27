<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 *          GNU Lesser General Public License, version 3 or later
 */
class SaveToAnyTableService
{

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
    protected $properties = [];

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
     * Unique identifier field
     *
     * @var string
     */
    protected $uniqueIdentifier = 'uid';

    /**
     * Additional where clause
     *
     * @var string
     */
    protected $additionalWhere;

    /**
     * Switch on devLog
     *
     * @var bool
     */
    protected $devLog = false;

    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $databaseConnection = null;

    /**
     * @param string $table
     */
    public function __construct($table)
    {
        $this->databaseConnection = ObjectUtility::getDatabaseConnection();
        $this->setTable($table);
    }

    /**
     * Executes the storage
     *
     * @return int uid of inserted record
     */
    public function execute()
    {
        $this->checkProperties();
        switch ($this->getMode()) {
            case 'update':
            case 'none':
                $this->checkIfIdentifierFieldExists();
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
     * @return int uid of inserted record
     */
    protected function insert()
    {
        $this->databaseConnection->exec_INSERTquery($this->getTable(), $this->getProperties());
        return $this->databaseConnection->sql_insert_id();
    }

    /**
     * Update existing record
     *
     * @return int uid of updated record
     */
    protected function update()
    {
        $row = $this->getExistingEntry();
        if (empty($row[$this->getUniqueIdentifier()])) {
            // if there is no existing entry, insert new one
            return $this->insert();
        }

        // update existing entry (only if mode is not "none")
        if ($this->getMode() !== 'none') {
            $this->databaseConnection->exec_UPDATEquery(
                $this->getTable(),
                $this->getUniqueIdentifier() . ' = ' . (int)$row[$this->getUniqueIdentifier()],
                $this->getProperties()
            );
        }

        return $row[$this->getUniqueIdentifier()];
    }

    /**
     * Check if there are properties
     *
     * @throws \Exception
     * @return void
     */
    protected function checkProperties()
    {
        if (empty($this->getProperties())) {
            throw new \Exception('No properties to insert/update given');
        }
    }

    /**
     * Set TableName
     *
     * @param string $table
     * @return void
     * @throws \Exception
     */
    public function setTable($table)
    {
        if (empty($table)) {
            throw new \Exception('No tablename given');
        }
        $this->removeNotAllowedSigns($table);
        $this->table = $table;
    }

    /**
     * Get TableName
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Read properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get one property value
     *
     * @param $propertyName
     * @return string
     */
    public function getProperty($propertyName)
    {
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
    public function addProperty($propertyName, $value)
    {
        $this->removeNotAllowedSigns($propertyName);
        $this->properties[$propertyName] = $value;
    }

    /**
     * Remove property/value pair form array by its key
     *
     * @param $propertyName
     * @return void
     */
    public function removeProperty($propertyName)
    {
        unset($this->properties[$propertyName]);
    }

    /**
     * @param string $mode
     * @return void
     */
    public function setMode($mode)
    {
        $possibleModes = [
            'insert',
            'update',
            'none'
        ];
        if (in_array($mode, $possibleModes)) {
            $this->mode = $mode;
        }
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $uniqueField
     * @return void
     */
    public function setUniqueField($uniqueField)
    {
        $this->uniqueField = $uniqueField;
    }

    /**
     * @return string
     */
    public function getUniqueField()
    {
        return $this->uniqueField;
    }

    /**
     * @return string
     */
    public function getUniqueIdentifier()
    {
        return $this->uniqueIdentifier;
    }

    /**
     * @param string $uniqueIdentifier
     * @return SaveToAnyTableService
     */
    public function setUniqueIdentifier($uniqueIdentifier)
    {
        $this->uniqueIdentifier = $uniqueIdentifier;
        return $this;
    }

    /**
     * @param boolean $devLog
     * @return void
     */
    public function setDevLog($devLog)
    {
        $this->devLog = $devLog;
    }

    /**
     * @return boolean
     */
    public function isDevLog()
    {
        return $this->devLog;
    }

    /**
     * @return string
     */
    public function getAdditionalWhere()
    {
        return $this->additionalWhere;
    }

    /**
     * @param string $additionalWhere
     */
    public function setAdditionalWhere($additionalWhere)
    {
        $this->additionalWhere = ' ' . $additionalWhere;
    }

    /**
     * Remove not allowed signs
     *
     * @param $string
     * @return void
     */
    protected function removeNotAllowedSigns(&$string)
    {
        $string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
    }

    /**
     * Write settings to devlog
     *
     * @return void
     */
    protected function writeToDevLog()
    {
        if ($this->isDevLog()) {
            $subject = 'SaveToAnyTable (Table: ' . $this->getTable();
            $subject .= ', Mode: ' . $this->getMode();
            $subject .= ', UniqueField: ' . $this->getUniqueField() . ')';
            GeneralUtility::devLog($subject, 'powermail', 0, $this->getProperties());
        }
    }

    /**
     * Find existing record in database
     *
     * @return array|FALSE|NULL
     */
    protected function getExistingEntry()
    {
        $searchterm = $this->databaseConnection->fullQuoteStr(
            $this->getProperty($this->getUniqueField()),
            $this->getTable()
        );
        $where = $this->getUniqueField() . ' = ' . $searchterm;
        $where .= $this->getDeletedWhereClause();
        $where .= $this->getAdditionalWhere();
        $row = $this->databaseConnection->exec_SELECTgetSingleRow($this->getUniqueIdentifier(), $this->getTable(), $where);
        return $row;
    }

    /**
     * @return string
     */
    protected function getDeletedWhereClause()
    {
        $where = '';
        if ($this->isFieldExisting('deleted')) {
            $where .= ' and deleted = 0';
        }
        return $where;
    }

    /**
     * @throws \Exception
     */
    protected function checkIfIdentifierFieldExists()
    {
        if (!$this->isFieldExisting($this->getUniqueIdentifier())) {
            throw new \Exception(
                'Field ' . $this->getUniqueIdentifier() . ' in table ' . $this->getTable() . ' does not exist,' .
                ' but it\'s needed for _ifUnique functionality'
            );
        }
    }

    /**
     * Check if field exists in table
     *
     * @param string $field
     * @return bool
     */
    protected function isFieldExisting($field)
    {
        $fields = $this->databaseConnection->admin_get_fields($this->getTable());
        return array_key_exists($field, $fields);
    }
}
