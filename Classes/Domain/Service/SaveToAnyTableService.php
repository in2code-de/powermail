<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\DriverException;
use In2code\Powermail\Exception\DatabaseFieldMissingException;
use In2code\Powermail\Exception\PropertiesMissingException;
use In2code\Powermail\Utility\DatabaseUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

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
    protected $additionalWhere = '';

    /**
     * @throws PropertiesMissingException
     */
    public function __construct(string $table)
    {
        $this->setTable($table);
    }

    /**
     * Executes the storage
     *
     * @return int uid of inserted record
     * @throws DBALException
     * @throws DatabaseFieldMissingException
     * @throws PropertiesMissingException
     */
    public function execute(): int
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
        try {
            return (int)$connection->lastInsertId();
        } catch (DriverException) {
            // The table has no uid, for example an mm table
            return 0;
        }
    }

    /**
     * Update existing record
     *
     * @return int uid of updated record
     * @throws DBALException
     */
    protected function update(): int
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
     * @throws PropertiesMissingException
     */
    protected function checkProperties(): void
    {
        if ($this->getProperties() === []) {
            throw new PropertiesMissingException('No properties to insert/update given', 1578607503);
        }
    }

    /**
     * Set TableName
     *
     * @throws PropertiesMissingException
     */
    public function setTable(string $table): void
    {
        if ($table === '' || $table === '0') {
            throw new PropertiesMissingException('No tablename given', 1578607506);
        }

        $this->removeNotAllowedSigns($table);
        $this->table = $table;
    }

    /**
     * Get TableName
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Read properties
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Get one property value
     */
    public function getProperty(string $propertyName): string
    {
        $currentProperty = '';
        $properties = $this->getProperties();
        if (array_key_exists($propertyName, $properties)) {
            return $properties[$propertyName];
        }

        return $currentProperty;
    }

    /**
     * Add property/value pair to array
     */
    public function addProperty(string $propertyName, string $value): void
    {
        $this->removeNotAllowedSigns($propertyName);
        $this->properties[$propertyName] = $value;
    }

    public function setMode(string $mode): void
    {
        $possibleModes = [
            self::MODE_INSERT,
            self::MODE_UPDATE,
            self::MODE_NONE,
        ];
        if (in_array($mode, $possibleModes)) {
            $this->mode = $mode;
        }
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setUniqueField(string $uniqueField): void
    {
        $this->uniqueField = $uniqueField;
    }

    public function getUniqueField(): string
    {
        return $this->uniqueField;
    }

    public function getUniqueIdentifier(): string
    {
        return $this->uniqueIdentifier;
    }

    public function getAdditionalWhere(): string
    {
        return $this->additionalWhere;
    }

    public function setAdditionalWhere(string $additionalWhere): void
    {
        $this->additionalWhere = ' ' . $additionalWhere;
    }

    /**
     * Remove not allowed signs
     */
    protected function removeNotAllowedSigns(string &$string): void
    {
        $string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
    }

    /**
     * Find existing record in database
     *
     * @throws DBALException
     */
    protected function getExistingEntry(): array
    {
        $queryBuilder = $this->getQueryBuilder();
        $searchterm = $queryBuilder->createNamedParameter($this->getProperty($this->getUniqueField()));
        $where = $this->getUniqueField() . ' = ' . $searchterm;
        $where .= $this->getDeletedWhereClause();
        $where .= $this->getAdditionalWhere();
        return (array)$queryBuilder
            ->select($this->getUniqueIdentifier())
            ->from($this->getTable())
            ->where($where)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();
    }

    /**
     * @throws DBALException
     */
    protected function getDeletedWhereClause(): string
    {
        $where = '';
        if ($this->isFieldExisting('deleted')) {
            $where .= ' and deleted = 0';
        }

        return $where;
    }

    /**
     * @throws DatabaseFieldMissingException
     * @throws DBALException
     */
    protected function checkIfIdentifierFieldExists(): void
    {
        if (!$this->isFieldExisting($this->getUniqueIdentifier())) {
            throw new DatabaseFieldMissingException(
                'Field ' . $this->getUniqueIdentifier() . ' in table ' . $this->getTable() . ' does not exist,' .
                " but it's needed for _ifUnique functionality",
                1579186701
            );
        }
    }

    /**
     * @throws DBALException
     */
    protected function isFieldExisting(string $field): bool
    {
        return DatabaseUtility::isFieldExistingInTable($field, $this->getTable());
    }

    protected function getConnection(): Connection
    {
        return DatabaseUtility::getConnectionForTable($this->getTable());
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return DatabaseUtility::getQueryBuilderForTable($this->getTable(), true);
    }
}
