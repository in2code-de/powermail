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

class PowermailPermissionUpdater implements UpgradeWizardInterface
{
    public function getIdentifier(): string
    {
        return 'txPowermailPluginPermissionUpdater';
    }

    public function getTitle(): string
    {
        return 'EXT:powermail: Migrate plugin permissions';
    }

    public function getDescription(): string
    {
        $description = 'This update wizard updates all permissions for plugins and modules';
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
            ->select('uid', 'explicit_allowdeny', 'groupMods')
            ->from('be_groups')
            ->where(
                $queryBuilder->expr()->like(
                    'explicit_allowdeny',
                    $queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards('tt_content:list_type:powermail_pi2') . '%')
                )
            )
            ->orWhere(
                $queryBuilder->expr()->like(
                    'explicit_allowdeny',
                    $queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards('tt_content:list_type:powermail_pi1') . '%')
                )
            )
            ->orWhere(
                $queryBuilder->expr()->like(
                    'groupMods',
                    $queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards('web_PowermailM1') . '%')
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    protected function updateRow(array $row): void
    {
        $pi1Replacement = 'tt_content:CType:powermail_pi1';
        $pi2Replacement = 'tt_content:CType:powermail_pi2,tt_content:CType:powermail_pi3,tt_content:CType:powermail_pi4';

        $searchReplace = [
            'tt_content:list_type:powermail_pi2:ALLOW' => $pi2Replacement,
            'tt_content:list_type:powermail_pi2:DENY' => '',
            'tt_content:list_type:powermail_pi2' => $pi2Replacement,
            'tt_content:list_type:powermail_pi1' => $pi1Replacement,
        ];

        $newList = str_replace(array_keys($searchReplace), array_values($searchReplace), $row['explicit_allowdeny']);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_groups');
        $queryBuilder->update('be_groups')
            ->set('explicit_allowdeny', $newList)
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter($row['uid'], Connection::PARAM_INT)
                )
            )
            ->executeStatement();

        $newModule = str_replace('web_PowermailM1', 'web_powermail', $row['groupMods']);
        $queryBuilder->update('be_groups')
            ->set('groupMods', $newModule)
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter($row['uid'], Connection::PARAM_INT)
                )
            )
            ->executeStatement();
    }
}
