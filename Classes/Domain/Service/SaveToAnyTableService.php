<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Utility\DatabaseUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class allows you to save values to any table in TYPO3 database
 *
 * Class SaveToAnyTableService
 */
class SaveToAnyTableService
{
    const MODE_INSERT = 'insert';
    const MODE_UPDATE = 'update';
    const MODE_NONE = 'none';

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
    protected $mode = self::MODE_INSERT;

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
     * @param string $table
     */
    public function __construct($table)
    {
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
            case self::MODE_UPDATE:
            case self::MODE_NONE:
                $this->checkIfIdentifierFieldExists();
                $uid = $this->update();
                break;

            case self::MODE_INSERT:
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
    protected function insert(): int
    {
        $connection = $this->getConnection();
        $connection->insert($this->getTable(), $this->getProperties());
        return (int)$connection->lastInsertId($this->getTable());
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
        if ($this->getMode() !== self::MODE_NONE) {
            $connection = $this->getConnection();
            $connection->update(
                $this->getTable(),
                $this->getProperties(),
                [$this->getUniqueIdentifier() => (int)$row[$this->getUniqueIdentifier()]]
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
            throw new \UnexpectedValueException('No properties to insert/update given');
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
            throw new \UnexpectedValueException('No tablename given');
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
        $currentProperty = '';
        $properties = $this->getProperties();
        if (array_key_exists($propertyName, $properties)) {
            $currentProperty = $properties[$propertyName];
        }
        return $currentProperty;
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
     * @param string $mode
     * @return void
     */
    public function setMode($mode)
    {
        $possibleModes = [
            self::MODE_INSERT,
            self::MODE_UPDATE,
            self::MODE_NONE
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
            $logger = ObjectUtility::getLogger(__CLASS__);
            $logger->info($subject, $this->getProperties());
        }
    }

    /**
     * Find existing record in database
     *
     * @return array|FALSE
     */
    protected function getExistingEntry()
    {
        $queryBuilder = $this->getQueryBuilder();
        $searchterm = $queryBuilder->createNamedParameter($this->getProperty($this->getUniqueField()));
        $where = $this->getUniqueField() . ' = ' . $searchterm;
        $where .= $this->getDeletedWhereClause();
        $where .= $this->getAdditionalWhere();
        $rows = $queryBuilder
            ->select($this->getUniqueIdentifier())
            ->from($this->getTable())
            ->where($where)
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();
        if (!empty($rows[0])) {
            return $rows[0];
        }
        return false;
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
            throw new \InvalidArgumentException(
                'Field ' . $this->getUniqueIdentifier() . ' in table ' . $this->getTable() . ' does not exist,' .
                ' but it\'s needed for _ifUnique functionality'
            );
        }
    }

    /**
     * @param string $field
     * @return bool
     */
    protected function isFieldExisting($field)
    {
        return DatabaseUtility::isFieldExistingInTable($field, $this->getTable());
    }

    /**
     * @return Connection
     */
    protected function getConnection(): Connection
    {
        return DatabaseUtility::getConnectionForTable($this->getTable());
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        return DatabaseUtility::getQueryBuilderForTable($this->getTable(), true);
    }
}
