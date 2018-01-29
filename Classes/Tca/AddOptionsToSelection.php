<?php
declare(strict_types=1);
namespace In2code\Powermail\Tca;

use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class AddOptionsToSelection allows to add individual options
 */
class AddOptionsToSelection
{

    /**
     * Parameters given from Backend
     *
     * @var array
     */
    protected $params = [];

    /**
     * Type of option: "type", "validation", "feUserProperty", "predefinedReceivers"
     *
     * @var string
     */
    protected $type = '';

    /**
     * @var \TYPO3\CMS\Lang\LanguageService
     */
    protected $languageService = null;

    /**
     * Add options to TCA Selection - Options can be defined in TSConfig
     *        Use page tsconfig in this way:
     *            tx_powermail.flexForm.type.addFieldOptions.newfield = New Field Name
     *            tx_powermail.flexForm.type.addFieldOptions.newfield =
     *                LLL:fileadmin/locallang.xlf:key
     *
     * @param array $params
     * @return void
     */
    public function addOptionsForType(&$params)
    {
        $this->initialize('type', $params);
        $this->addOptions();
    }

    /**
     * Add options to TCA Selection - Options can be defined in TSConfig
     *        Use page tsconfig in this way:
     *            tx_powermail.flexForm.validation.addFieldOptions.100 = New Validation
     *            tx_powermail.flexForm.validation.addFieldOptions.100 =
     *                LLL:fileadmin/locallang.xlf:key
     *
     * @param array $params
     * @return void
     */
    public function addOptionsForValidation(&$params)
    {
        $this->initialize('validation', $params);
        $this->addOptions();
    }

    /**
     * Add options to TCA Selection - Options can be defined in TSConfig
     *        Use page tsconfig in this way:
     *            tx_powermail.flexForm.feUserProperty.addFieldOptions.newfield = New fe_user
     *            tx_powermail.flexForm.feUserProperty.addFieldOptions.newfield =
     *                LLL:fileadmin/locallang.xlf:key
     *
     * @param array $params
     * @return void
     */
    public function addOptionsForFeUserProperty(&$params)
    {
        $this->initialize('feUserProperty', $params);
        $this->addOptions();
    }

    /**
     * Add options to FlexForm Selection - Options can be defined in TSConfig
     *        Use page tsconfig in this way:
     *            tx_powermail.flexForm.predefinedReceivers.addFieldOptions.receivers1 = receivers #1
     *            tx_powermail.flexForm.predefinedReceivers.addFieldOptions.receivers1 =
     *                LLL:fileadmin/locallang.xlf:key
     *
     * @param array $params
     * @return void
     */
    public function addOptionsForPredefinedReceivers(&$params)
    {
        $this->initialize('predefinedReceivers', $params);
        $this->addOptions();
    }

    /**
     * Add options to FlexForm Selection
     *
     * @return void
     */
    protected function addOptions()
    {
        foreach ($this->getFieldOptionsFromTsConfig() as $value => $label) {
            if (StringUtility::endsWith((string)$value, '.') === false) {
                $this->addOption($value, $label);
            }
        }
    }

    /**
     * Get field options from page TSConfig
     *
     * @return array
     */
    protected function getFieldOptionsFromTsConfig()
    {
        $fieldOptions = [];
        $tsConfiguration = BackendUtility::getPagesTSconfig($this->getPageIdentifier());
        $eConfiguration = $tsConfiguration['tx_powermail.']['flexForm.'];

        if (!empty($eConfiguration[$this->getType() . '.']['addFieldOptions.'])) {
            $options = $eConfiguration[$this->getType() . '.']['addFieldOptions.'];
            if (is_array($options)) {
                $fieldOptions = $options;
            }
        }

        return $fieldOptions;
    }

    /**
     * Add item to $this->params['items'] with value and label
     *
     * @param string $value
     * @param null|string $label
     */
    protected function addOption($value, $label = null)
    {
        $this->params['items'][] = [
            $this->getLabel($label, $value),
            $value
        ];
    }

    /**
     * Return label
     *        if LLL parse
     *        if empty take value
     *
     * @param null|string $label
     * @param string $fallback
     * @return string
     */
    protected function getLabel($label, $fallback)
    {
        if (strpos($label, 'LLL:') === 0) {
            $label = $this->languageService->sL($label);
        }
        if (empty($label)) {
            $label = $fallback;
        }
        return $label;
    }

    /**
     * Get current PID (starting from TCA or FlexForm)
     *
     * @return int
     */
    protected function getPageIdentifier()
    {
        $pageIdentifier = 0;
        if (!empty($this->params['row']['pid'])) {
            $pageIdentifier = (int)$this->params['row']['pid'];
        }
        if (!empty($this->params['flexParentDatabaseRow']['pid'])) {
            $pageIdentifier = (int)$this->params['flexParentDatabaseRow']['pid'];
        }
        return $pageIdentifier;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return AddOptionsToSelection
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Initialize
     *
     * @param string $type "type", "validation", "feUserProperty"
     * @param array $params
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function initialize($type, &$params)
    {
        $this->setType($type);
        $this->params = $params;
        $this->languageService = $GLOBALS['LANG'];
    }
}
