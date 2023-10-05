<?php

declare(strict_types=1);

/*
 * This file is part of the "powermail" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace In2code\Powermail\Update;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class PowermailPermissionSubmoduleUpdater implements UpgradeWizardInterface
{
    public function getIdentifier(): string
    {
        return 'txPowermailPluginPermissionSubmodulesUpdater';
    }

    public function getTitle(): string
    {
        return 'EXT:powermail: Grant permissions for submodules to editors';
    }

    public function getDescription(): string
    {
        $description = 'This update wizard migrates be groups, that use the old main module to the new submodules ';
        $description .= ' Count of affected groups: ' . count($this->getMigrationRecords());
        return $description;
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    public function updateNecessary(): bool
    {
        return $this->checkIfWizardIsRequired();
    }

    public function executeUpdate(): bool
    {
        return $this->performMigration();
    }

    public function checkIfWizardIsRequired(): bool
    {
        return count($this->getMigrationRecords()) > 0;
    }

    public function performMigration(): bool
    {
        $records = $this->getMigrationRecords();

        foreach ($records as $record) {
            $this->updateRow($record);
        }

        return true;
    }

    protected function getMigrationRecords(): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('be_groups');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder
            ->select('uid', 'groupMods')
            ->from('be_groups')
            ->where(
                $queryBuilder->expr()->like(
                    'groupMods',
                    $queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards('web_powermail') . '%')
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    protected function updateRow(array $row): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_groups');

        $newModules = str_replace('web_powermail', 'web_powermail,powermail_list,powermail_overview_be,powermail_reporting_form,powermail_reporting_marketing,powermail_check_be', $row['groupMods']);
        $queryBuilder->update('be_groups')
            ->set('groupMods', $newModules)
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter($row['uid'], Connection::PARAM_INT)
                )
            )
            ->executeStatement();
    }
}
