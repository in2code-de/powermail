<?php

declare(strict_types=1);
namespace In2code\Powermail\Utility;

use Doctrine\DBAL\DBALException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class DatabaseUtility
 * @codeCoverageIgnore
 */
class DatabaseUtility
{
    /**
     * @param string $tableName
     * @param bool $removeRestrictions
     * @return QueryBuilder
     */
    public static function getQueryBuilderForTable(string $tableName, bool $removeRestrictions = false): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        if ($removeRestrictions === true) {
            $queryBuilder->getRestrictions()->removeAll();
        }
        return $queryBuilder;
    }

    /**
     * @param string $tableName
     * @return Connection
     */
    public static function getConnectionForTable(string $tableName): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);
    }

    /**
     * @param string $tableName
     * @return bool
     * @throws DBALException
     */
    public static function isTableExisting(string $tableName): bool
    {
        $existing = false;
        $connection = self::getConnectionForTable($tableName);
        $queryResult = $connection->executeQuery('show tables;')->fetchAllAssociative();
        foreach ($queryResult as $tableProperties) {
            if (in_array($tableName, array_values($tableProperties))) {
                $existing = true;
                break;
            }
        }
        return $existing;
    }

    public static function getPidForRecord(int $uid, string $tableName): int
    {
        $queryBuilder = self::getQueryBuilderForTable($tableName);
        $pid = $queryBuilder
            ->select('pid')
            ->from($tableName)
            ->where('uid = ' . $uid)
            ->executeQuery()
            ->fetchOne();

        return (int)$pid;
    }

    /**
     * @param string $fieldName
     * @param string $tableName
     * @return bool
     * @throws DBALException
     */
    public static function isFieldExistingInTable(string $fieldName, string $tableName): bool
    {
        $found = false;
        $connection = self::getConnectionForTable($tableName);
        $queryResult = $connection->executeQuery('describe ' . $tableName . ';')->fetchAllAssociative();
        foreach ($queryResult as $fieldProperties) {
            if ($fieldProperties['Field'] === $fieldName) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    /**
     * Check if there are any values in a table field (don't care about deleted property)
     *
     * @param string $fieldName
     * @param string $tableName
     * @return bool
     * @throws DBALException
     */
    public static function isFieldFilled(string $fieldName, string $tableName): bool
    {
        if (self::isFieldExistingInTable($fieldName, $tableName)) {
            $queryBuilder = self::getQueryBuilderForTable($tableName, true);
            return (int)$queryBuilder
                    ->count($fieldName)
                    ->from($tableName)
                    ->where($fieldName . ' != \'\' and ' . $fieldName . ' != 0')
                    ->executeQuery()
                    ->fetchOne() > 0;
        }
        return false;
    }

    public static function deleteMailAndAnswersFromDatabase(int $mailUid): void
    {
        $queryBuilderAnswer = DatabaseUtility::getQueryBuilderForTable('tx_powermail_domain_model_answer');
        $queryBuilderAnswer
            ->delete('tx_powermail_domain_model_answer')
            ->where(
                $queryBuilderAnswer->expr()->eq(
                    'mail',
                    $queryBuilderAnswer->createNamedParameter($mailUid)
                )
            )
            ->executeStatement();

        $queryBuilderMail = DatabaseUtility::getQueryBuilderForTable('tx_powermail_domain_model_mail');
        $queryBuilderMail
            ->delete('tx_powermail_domain_model_mail')
            ->where(
                $queryBuilderMail->expr()->eq(
                    'uid',
                    $queryBuilderMail->createNamedParameter($mailUid)
                )
            )
            ->executeStatement();
    }
}
