<?php

declare(strict_types=1);

namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\SaveToAnyTableService;
use In2code\Powermail\Exception\DatabaseFieldMissingException;
use In2code\Powermail\Exception\PropertiesMissingException;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class SaveToAnyTableFinisher
 */
class SaveToAnyTableFinisher extends AbstractFinisher implements FinisherInterface
{
    /**
     * @var ContentObjectRenderer
     * local instance that can be manipulated via start() and has no influence to parent::contentObject
     */
    protected ContentObjectRenderer $contentObjectLocal;

    protected array $dataArray = [];

    public function __construct(
        Mail $mail,
        array $configuration,
        array $settings,
        bool $formSubmitted,
        string $actionMethodName,
        ContentObjectRenderer $contentObject
    ) {
        parent::__construct($mail, $configuration, $settings, $formSubmitted, $actionMethodName, $contentObject);
        GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        $this->contentObjectLocal = $this->contentObject;
    }

    /**
     * @throws DatabaseFieldMissingException
     * @throws PropertiesMissingException
     */
    public function savePreflightFinisher(): void
    {
        if ($this->isConfigurationAvailable()) {
            foreach (array_keys($this->configuration) as $key) {
                $this->contentObjectLocal->start($this->getDataArray());
                $tableConfiguration = $this->configuration[$key];
                $numberKey = (int)StringUtility::removeLastDot($key);
                if ($this->isSaveToAnyTableActivatedForSpecifiedTable($tableConfiguration)) {
                    $this->saveSpecifiedTablePreflight($numberKey, $tableConfiguration);
                }
            }
        }
    }

    /**
     * Preperation function for a single table
     *
     * @throws DatabaseFieldMissingException
     * @throws PropertiesMissingException
     */
    protected function saveSpecifiedTablePreflight(int $numberKey, array $tableConfiguration): void
    {
        /* @var $saveService SaveToAnyTableService */
        $saveService = GeneralUtility::makeInstance(
            SaveToAnyTableService::class,
            $this->getTableName($tableConfiguration)
        );
        $this->setModeInSaveService($saveService, $tableConfiguration);
        $this->setPropertiesInSaveService($saveService, $tableConfiguration);
        $this->addArrayToDataArray(['uid_' . $numberKey => (int)$saveService->execute()]);
    }

    /**
     * Set all properties for a table configuration
     */
    protected function setPropertiesInSaveService(SaveToAnyTableService $saveService, array $tableConfiguration): void
    {
        foreach (array_keys($tableConfiguration) as $field) {
            if (!$this->isSkippedKey($field)) {
                $value = $this->contentObjectLocal->cObjGetSingle(
                    $tableConfiguration[$field],
                    $tableConfiguration[$field . '.']
                );
                // @extensionScannerIgnoreLine Seems to be a false positive: addProperty()
                $saveService->addProperty($field, $value);
            }
        }
    }

    /**
     * Set mode and uniqueField in saveToAnyTableService
     */
    protected function setModeInSaveService(SaveToAnyTableService $saveService, array $tableConfiguration): void
    {
        if (!empty($tableConfiguration['_ifUnique.'])) {
            $uniqueFields = array_keys($tableConfiguration['_ifUnique.']);
            $saveService->setMode($tableConfiguration['_ifUnique.'][$uniqueFields[0]]);
            $saveService->setUniqueField($uniqueFields[0]);
            $this->addAdditionalWhereClause($saveService, $tableConfiguration);
        }
    }

    /**
     * add additional Where clause from configuration
     *
     *      _ifUniqueWhereClause = TEXT
     *      _ifUniqueWhereClause.value = AND pid = 123
     *
     *      or
     *
     *      _ifUniqueWhereClause = AND pid = 123
     */
    protected function addAdditionalWhereClause(SaveToAnyTableService $saveService, array $tableConfiguration): void
    {
        $whereClause = '';
        if (!empty($tableConfiguration['_ifUniqueWhereClause'])
            && empty($tableConfiguration['_ifUniqueWhereClause.'])) {
            $whereClause = $tableConfiguration['_ifUniqueWhereClause'];
        }

        if (!empty($tableConfiguration['_ifUniqueWhereClause'])
            && !empty($tableConfiguration['_ifUniqueWhereClause.'])) {
            $whereClause = $this->contentObjectLocal->cObjGetSingle(
                $tableConfiguration['_ifUniqueWhereClause'],
                $tableConfiguration['_ifUniqueWhereClause.']
            );
        }

        if (!empty($whereClause)) {
            $saveService->setAdditionalWhere($whereClause);
        }
    }

    /**
     * Read configuration from TypoScript
     *      _table = TEXT
     *      _table.value = tableName
     */
    protected function getTableName(array $tableConfiguration): string
    {
        return $this->contentObjectLocal->cObjGetSingle($tableConfiguration['_table'], $tableConfiguration['_table.']);
    }

    /**
     * @param array $tableConfiguration
     */
    protected function isSaveToAnyTableActivatedForSpecifiedTable($tableConfiguration): bool
    {
        $enable = $this->contentObjectLocal->cObjGetSingle(
            $tableConfiguration['_enable'] ?? '',
            $tableConfiguration['_enable.'] ?? null
        );
        return !empty($enable);
    }

    /**
     * Check if plugin.tx_powermail.settings.setup.dbEntry is not empty
     */
    protected function isConfigurationAvailable(): bool
    {
        return $this->configuration !== [] && is_array($this->configuration);
    }

    /**
     * Should this key skipped because it starts with _ or ends with .
     */
    protected function isSkippedKey(string $key): bool
    {
        if (StringUtility::startsWith($key, '_')) {
            return true;
        }
        return StringUtility::endsWith($key, '.');
    }

    /**
     * Add array to dataArray
     */
    protected function addArrayToDataArray(array $array): void
    {
        $dataArray = $this->getDataArray();
        $dataArray = array_merge($dataArray, $array);
        $this->setDataArray($dataArray);
    }

    public function getDataArray(): array
    {
        return $this->dataArray;
    }

    public function setDataArray(array $dataArray): SaveToAnyTableFinisher
    {
        $this->dataArray = $dataArray;
        return $this;
    }

    public function initializeFinisher(): void
    {
        $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
        $configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
        if (!empty($configuration['dbEntry.'])) {
            $this->configuration = $configuration['dbEntry.'];
        }

        if ($this->isConfigurationAvailable()) {
            $this->addArrayToDataArray(['uid' => $this->mail->getUid()]);
            $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
            $this->addArrayToDataArray($mailRepository->getVariablesWithMarkersFromMail($this->mail));
        }
    }
}
