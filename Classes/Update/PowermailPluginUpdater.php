<?php

declare(strict_types=1);

namespace In2code\Powermail\Update;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class PowermailPluginUpdater implements UpgradeWizardInterface
{
    private const MIGRATION_SETTINGS = [
        [
            'switchableControllerActions' => 'Output->list;Output->show;Output->export;Output->rss;',
            'targetListType' => 'powermail_pi2',
        ],
        [
            'switchableControllerActions' => 'Output->edit;Output->update;Output->delete;',
            'targetListType' => 'powermail_pi3',
        ],
        [
            'switchableControllerActions' => 'Output->list;Output->show;Output->export;Output->rss;Output->edit;Output->update;Output->delete;',
            'targetListType' => 'powermail_pi4',
        ],
    ];

    private const LIST_TYPES = [
        'powermail_pi1',
        'powermail_pi2',
    ];

    /** @var FlexFormService */
    protected $flexFormService;

    public function __construct()
    {
        $this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'powermailPluginUpdater';
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Powermail: Migrate list types and switchable controller actions to CTypes';
    }

    /**
     * Return the description for this wizard
     *
     * @return string
     */
    public function getDescription(): string
    {
        $description = 'The old plugin using switchableControllerActions has been split into separate plugins. ';
        $description .= 'This update wizard migrates all existing plugin settings and changes the plugin';
        $description .= 'to use the new plugins available.';
        $description .= 'Count of plugins "Powermail": ' . count($this->getMigrationRecords('powermail_pi1'));
        $description .= 'Count of plugins "Powermail Frontend": ' . count($this->getMigrationRecords('powermail_pi2'));
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
        $success = false;
        foreach (self::LIST_TYPES as $list_type) {
            $success = $this->performMigration($list_type);
        }
        return $success;
    }

    public function checkIfWizardIsRequired(): bool
    {
        $recordsToUpdate = 0;
        foreach (self::LIST_TYPES as $list_type) {
            $recordsToUpdate += count($this->getMigrationRecords($list_type));
        }
        return $recordsToUpdate > 0;
    }

    public function performMigration(string $list_type): bool
    {
        $records = $this->getMigrationRecords($list_type);

        foreach ($records as $record) {
            $flexFormData = GeneralUtility::xml2array($record['pi_flexform']);
            $flexForm = $this->flexFormService->convertFlexFormContentToArray($record['pi_flexform']);

            if ($list_type === 'powermail_pi2') {
                $targetCType = $this->getTargetListType($flexForm['switchableControllerActions']);
                $allowedSettings = $this->getAllowedSettingsFromFlexForm($targetCType);
                foreach ($flexFormData['data'] as $sheetKey => $sheetData) {
                    foreach ($sheetData['lDEF'] as $settingName => $setting) {
                        if (!in_array($settingName, $allowedSettings, true)) {
                            unset($flexFormData['data'][$sheetKey]['lDEF'][$settingName]);
                        }
                    }

                    // Remove empty sheets
                    if (!count($flexFormData['data'][$sheetKey]['lDEF']) > 0) {
                        unset($flexFormData['data'][$sheetKey]);
                    }
                }

                if (count($flexFormData['data']) > 0) {
                    $newFlexform = $this->array2xml($flexFormData);
                } else {
                    $newFlexform = '';
                }
            } else {
                $targetCType = 'powermail_pi1';
                $newFlexform = $record['pi_flexform'];
            }

            // Remove flexform data which do not exist in flexform of new plugin
            $this->updateContentElement($record['uid'], $targetCType, $newFlexform);
        }

        return true;
    }

    protected function getMigrationRecords($list_type): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder
            ->select('uid', 'list_type', 'pi_flexform')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'list_type',
                    $queryBuilder->createNamedParameter($list_type)
                ),
                $queryBuilder->expr()->eq(
                    'CType',
                    $queryBuilder->createNamedParameter('list')
                ),
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    protected function getTargetListType(string $switchableControllerActions): string
    {
        foreach (self::MIGRATION_SETTINGS as $setting) {
            if ($setting['switchableControllerActions'] === $switchableControllerActions
            ) {
                return $setting['targetListType'];
            }
        }

        return '';
    }

    protected function getAllowedSettingsFromFlexForm(string $listType): array
    {
        $flexFormFile = $GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds']['*,' . $listType];
        $flexFormContent = file_get_contents(GeneralUtility::getFileAbsFileName(substr(trim($flexFormFile), 5)));
        $flexFormData = GeneralUtility::xml2array($flexFormContent);

        // Iterate each sheet and extract all settings
        $settings = [];
        foreach ($flexFormData['sheets'] as $sheet) {
            foreach ($sheet['ROOT']['el'] as $setting => $tceForms) {
                $settings[] = $setting;
            }
        }

        return $settings;
    }

    /**
     * Updates list_type and pi_flexform of the given content element UID
     *
     * @param int $uid
     * @param string $newCType
     * @param string $flexform
     */
    protected function updateContentElement(int $uid, string $newCType, string $flexform): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->update('tt_content')
            ->set('CType', $newCType)
            ->set('list_type', '')
            ->set('pi_flexform', $flexform)
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)
                )
            )
            ->executeStatement();
    }

    /**
     * Transforms the given array to FlexForm XML
     *
     * @param array $input
     * @return string
     */
    protected function array2xml(array $input = []): string
    {
        $options = [
            'parentTagMap' => [
                'data' => 'sheet',
                'sheet' => 'language',
                'language' => 'field',
                'el' => 'field',
                'field' => 'value',
                'field:el' => 'el',
                'el:_IS_NUM' => 'section',
                'section' => 'itemType',
            ],
            'disableTypeAttrib' => 2,
        ];
        $spaceInd = 4;
        $output = GeneralUtility::array2xml($input, '', 0, 'T3FlexForms', $spaceInd, $options);
        $output = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . LF . $output;
        return $output;
    }
}
