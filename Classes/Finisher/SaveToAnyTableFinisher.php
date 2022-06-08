<?php
declare(strict_types = 1);
namespace In2code\Powermail\Finisher;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\SaveToAnyTableService;
use In2code\Powermail\Exception\DatabaseFieldMissingException;
use In2code\Powermail\Exception\PropertiesMissingException;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;
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
    protected $contentObjectLocal;

    /**
     * @var array
     */
    protected $dataArray = [];

    /**
     * @param Mail $mail
     * @param array $configuration
     * @param array $settings
     * @param bool $formSubmitted
     * @param string $actionMethodName
     * @param ContentObjectRenderer $contentObject
     */
    public function __construct(
        Mail $mail,
        array $configuration,
        array $settings,
        bool $formSubmitted,
        string $actionMethodName,
        ContentObjectRenderer $contentObject
    ) {
        parent::__construct($mail, $configuration, $settings, $formSubmitted, $actionMethodName, $contentObject);
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        $this->contentObjectLocal = $configurationManager->getContentObject();
    }

    /**
     * Preperation function for every table
     *
     * @return void
     * @throws DBALException
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
     * @param int $numberKey
     * @param array $tableConfiguration
     * @return void
     * @throws DBALException
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
        $saveService->setDevLog(!empty($this->settings['debug']['saveToTable']));
        $this->addArrayToDataArray(['uid_' . $numberKey => (int)$saveService->execute()]);
    }

    /**
     * Set all properties for a table configuration
     *
     * @param SaveToAnyTableService $saveService
     * @param array $tableConfiguration
     * @return void
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
     *
     * @param SaveToAnyTableService $saveService
     * @param array $tableConfiguration
     * @return void
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
     *
     * @param SaveToAnyTableService $saveService
     * @param array $tableConfiguration
     * @return void
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
     *
     * @param array $tableConfiguration
     * @return string
     */
    protected function getTableName(array $tableConfiguration): string
    {
        return $this->contentObjectLocal->cObjGetSingle($tableConfiguration['_table'], $tableConfiguration['_table.']);
    }

    /**
     * @param array $tableConfiguration
     * @return bool
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
     *
     * @return bool
     */
    protected function isConfigurationAvailable(): bool
    {
        return !empty($this->configuration) && is_array($this->configuration);
    }

    /**
     * Should this key skipped because it starts with _ or ends with .
     *
     * @param string $key
     * @return bool
     */
    protected function isSkippedKey(string $key): bool
    {
        return StringUtility::startsWith($key, '_') || StringUtility::endsWith($key, '.');
    }

    /**
     * Add array to dataArray
     *
     * @param array $array
     * @return void
     */
    protected function addArrayToDataArray(array $array): void
    {
        $dataArray = $this->getDataArray();
        $dataArray = array_merge($dataArray, $array);
        $this->setDataArray($dataArray);
    }

    /**
     * @return array
     */
    public function getDataArray(): array
    {
        return $this->dataArray;
    }

    /**
     * @param array $dataArray
     * @return SaveToAnyTableFinisher
     */
    public function setDataArray(array $dataArray): SaveToAnyTableFinisher
    {
        $this->dataArray = $dataArray;
        return $this;
    }

    /**
     * @return void
     * @throws Exception
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function initializeFinisher(): void
    {
        $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
        $configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
        if (!empty($configuration['dbEntry.'])) {
            $this->configuration = $configuration['dbEntry.'];
        }
        if ($this->isConfigurationAvailable()) {
            $this->addArrayToDataArray(['uid' => $this->mail->getUid()]);
            $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
            $this->addArrayToDataArray($mailRepository->getVariablesWithMarkersFromMail($this->mail));
        }
    }
}
