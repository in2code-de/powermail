<?php
declare(strict_types=1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\SaveToAnyTableService;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class SaveToAnyTableFinisher
 */
class SaveToAnyTableFinisher extends AbstractFinisher implements FinisherInterface
{

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @var array
     */
    protected $dataArray = [];

    /**
     * Preperation function for every table
     *
     * @return void
     */
    public function savePreflightFinisher()
    {
        if ($this->isConfigurationAvailable()) {
            foreach (array_keys($this->configuration) as $key) {
                $this->contentObject->start($this->getDataArray());
                $tableConfiguration = $this->configuration[$key];
                $numberKey = StringUtility::removeLastDot($key);
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
     */
    protected function saveSpecifiedTablePreflight($numberKey, array $tableConfiguration)
    {
        /* @var $saveService SaveToAnyTableService */
        $saveService = ObjectUtility::getObjectManager()->get(
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
    protected function setPropertiesInSaveService(SaveToAnyTableService $saveService, array $tableConfiguration)
    {
        foreach (array_keys($tableConfiguration) as $field) {
            if (!$this->isSkippedKey($field)) {
                $value = $this->contentObject->cObjGetSingle(
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
    protected function setModeInSaveService(SaveToAnyTableService $saveService, array $tableConfiguration)
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
    protected function addAdditionalWhereClause(SaveToAnyTableService $saveService, array $tableConfiguration)
    {
        $whereClause = '';
        if (!empty($tableConfiguration['_ifUniqueWhereClause'])
            && empty($tableConfiguration['_ifUniqueWhereClause.'])) {
            $whereClause = $tableConfiguration['_ifUniqueWhereClause'];
        }
        if (!empty($tableConfiguration['_ifUniqueWhereClause'])
            && !empty($tableConfiguration['_ifUniqueWhereClause.'])) {
            $whereClause = $this->contentObject->cObjGetSingle(
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
    protected function getTableName(array $tableConfiguration)
    {
        return $this->contentObject->cObjGetSingle($tableConfiguration['_table'], $tableConfiguration['_table.']);
    }

    /**
     * @param array $tableConfiguration
     * @return bool
     */
    protected function isSaveToAnyTableActivatedForSpecifiedTable($tableConfiguration)
    {
        $enable = $this->contentObject->cObjGetSingle($tableConfiguration['_enable'], $tableConfiguration['_enable.']);
        return !empty($enable);
    }

    /**
     * Check if plugin.tx_powermail.settings.setup.dbEntry is not empty
     *
     * @return bool
     */
    protected function isConfigurationAvailable()
    {
        return !empty($this->configuration) && is_array($this->configuration);
    }

    /**
     * Should this key skipped because it starts with _ or ends with .
     *
     * @param string $key
     * @return bool
     */
    protected function isSkippedKey($key)
    {
        return StringUtility::startsWith($key, '_') || StringUtility::endsWith($key, '.');
    }

    /**
     * Add array to dataArray
     *
     * @param array $array
     * @return void
     */
    protected function addArrayToDataArray(array $array)
    {
        $dataArray = $this->getDataArray();
        $dataArray = array_merge($dataArray, $array);
        $this->setDataArray($dataArray);
    }

    /**
     * @return array
     */
    public function getDataArray()
    {
        return $this->dataArray;
    }

    /**
     * @param array $dataArray
     * @return SaveToAnyTableFinisher
     */
    public function setDataArray(array $dataArray)
    {
        $this->dataArray = $dataArray;
        return $this;
    }

    /**
     * Initialize
     */
    public function initializeFinisher()
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

    /**
     * @param ContentObjectRenderer $contentObject
     * @return void
     */
    public function injectContentObject(ContentObjectRenderer $contentObject)
    {
        $this->contentObject = $contentObject;
    }
}
