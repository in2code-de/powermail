<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Utility\LocalizationUtility;
use In2code\Powermail\Utility\ObjectUtility;

/**
 * Class ConvertTableNamesService to convert old table names of powermail 2.x to 3.x
 *
 * @package In2code\Powermail\Domain\Service
 */
class ConvertTableNamesService
{

    /**
     * @var array
     */
    protected $oldTableNames = [
        'tx_powermail_domain_model_forms',
        'tx_powermail_domain_model_pages',
        'tx_powermail_domain_model_fields',
        'tx_powermail_domain_model_mails',
        'tx_powermail_domain_model_answers',
    ];

    /**
     * @var array
     */
    protected $newTableNames = [
        'tx_powermail_domain_model_form',
        'tx_powermail_domain_model_page',
        'tx_powermail_domain_model_field',
        'tx_powermail_domain_model_mail',
        'tx_powermail_domain_model_answer',
    ];

    /**
     * Try to convert old to new table names
     * Should work even if there are still new tables but they MUST be empty
     *
     * @return string HTML
     */
    public function convert()
    {
        if ($this->isOneOfOldTablesMissing()) {
            return LocalizationUtility::translate('ExtensionManagerConvertingScriptStopNoTablesFound');
        }
        if (!$this->newTablesPrepared()) {
            return LocalizationUtility::translate('ExtensionManagerConvertingScriptStopAlreadyExist');
        }
        return $this->duplicateTables();
    }

    /**
     * Check if one of the old tables is missing
     *
     * @return bool
     */
    protected function isOneOfOldTablesMissing()
    {
        $allTables = ObjectUtility::getDatabaseConnection()->admin_get_tables();
        foreach ($this->oldTableNames as $oldTableName) {
            if (!in_array($oldTableName, array_keys($allTables))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Clone old to new tables with structure (if not yet existing) and data
     *
     * @return string
     */
    protected function duplicateTables()
    {
        foreach (array_keys($this->oldTableNames) as $key) {
            $oldTable = $this->oldTableNames[$key];
            $newTable = $this->newTableNames[$key];

            /**
             * Drop table if empty
             * to ensure that all fields (maybe tables are extended from other extensions)
             * are cloned
             */
            if ($this->isTableAlreadyExisting($newTable) && $this->isTableEmpty($newTable)) {
                $this->dropTable($newTable);
            }

            $this->cloneTableStructure($oldTable, $newTable);
            $this->cloneTableData($oldTable, $newTable);
        }
        return LocalizationUtility::translate('ExtensionManagerConvertingScriptSuccess');
    }

    /**
     * Create table structure from existing table
     *
     * @param string $oldTable
     * @param string $newTable
     * @return void
     */
    protected function cloneTableStructure($oldTable, $newTable)
    {
        ObjectUtility::getDatabaseConnection()->admin_query(
            'CREATE TABLE ' . $newTable . ' LIKE ' . $oldTable . ';'
        );
    }

    /**
     * Fill table data from existing table
     *
     * @param string $oldTable
     * @param string $newTable
     * @return void
     */
    protected function cloneTableData($oldTable, $newTable)
    {
        ObjectUtility::getDatabaseConnection()->admin_query(
            'INSERT ' . $newTable . ' SELECT * FROM ' . $oldTable . ';'
        );
    }

    /**
     * @param string $table
     * @return void
     */
    protected function dropTable($table)
    {
        ObjectUtility::getDatabaseConnection()->admin_query(
            'DROP TABLE ' . $table . ';'
        );
    }

    /**
     * Check if new tables are not there OR
     * if they are there but they are still empty
     *
     * @return bool
     */
    protected function newTablesPrepared()
    {
        return !$this->areNewTablesAlreadyExisting() ||
            ($this->areNewTablesAlreadyExisting() && $this->areNewTablesEmpty());
    }

    /**
     * Check if one of the new tables already exists
     * Turn function off if dontCheckNewTables is set to true
     *
     * @return bool
     */
    protected function areNewTablesAlreadyExisting()
    {
        $allTables = ObjectUtility::getDatabaseConnection()->admin_get_tables();
        foreach (array_keys($allTables) as $existingTable) {
            if (in_array($existingTable, $this->newTableNames)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $table
     * @return bool
     */
    protected function isTableAlreadyExisting($table)
    {
        $allTables = ObjectUtility::getDatabaseConnection()->admin_get_tables();
        return in_array($table, array_keys($allTables));
    }

    /**
     * Check if the new tables are empty
     *
     * @return bool
     */
    protected function areNewTablesEmpty()
    {
        foreach ($this->newTableNames as $newTable) {
            if (!$this->isTableEmpty($newTable)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if a specific table is empty
     *
     * @param string $table
     * @return bool
     */
    protected function isTableEmpty($table)
    {
        $row = ObjectUtility::getDatabaseConnection()->exec_SELECTgetSingleRow('uid', $table, '1');
        return empty($row['uid']);
    }
}
