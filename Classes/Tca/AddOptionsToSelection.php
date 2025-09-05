<?php

declare(strict_types=1);
namespace In2code\Powermail\Tca;

use In2code\Powermail\Utility\ObjectUtility;
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
     * @param string $type "type", "validation", "feUserProperty"
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function initialize(string $type, array &$params): void
    {
        $this->setType($type);
        $this->params = $params;
    }

    /**
     * Add options to TCA Selection - Options can be defined in TSConfig
     *        Use page tsconfig in this way:
     *            tx_powermail.flexForm.type.addFieldOptions.newfield = New Field Name
     *            tx_powermail.flexForm.type.addFieldOptions.newfield =
     *                LLL:fileadmin/locallang.xlf:key
     */
    public function addOptionsForType(array &$params): void
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
     */
    public function addOptionsForValidation(array &$params): void
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
     */
    public function addOptionsForFeUserProperty(array &$params): void
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
     */
    public function addOptionsForPredefinedReceivers(array &$params): void
    {
        $this->initialize('predefinedReceivers', $params);
        $this->addOptions();
    }

    /**
     * Add options to FlexForm Selection
     */
    protected function addOptions(): void
    {
        foreach ($this->getFieldOptionsFromTsConfig() as $value => $label) {
            if (StringUtility::endsWith((string)$value, '.') === false) {
                $this->addOption((string)$value, $label);
            }
        }
    }

    /**
     * Get field options from page TSConfig
     */
    protected function getFieldOptionsFromTsConfig(): array
    {
        $fieldOptions = [];
        $tsConfiguration = BackendUtility::getPagesTSconfig($this->getPageIdentifier());
        $eConfiguration = $tsConfiguration['tx_powermail.']['flexForm.'] ?? [];

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
     * @param string|null $label
     */
    protected function addOption(string $value, ?string $label = null): void
    {
        $this->params['items'][] = [
            $this->getLabel($label, $value),
            $value,
        ];
    }

    /**
     * Return label
     *        if LLL parse
     *        if empty take value
     */
    protected function getLabel(string $label, string $fallback): string
    {
        if (str_starts_with($label, 'LLL:')) {
            $label = ObjectUtility::getLanguageService()->sL($label);
        }

        if ($label === '' || $label === '0') {
            return $fallback;
        }

        return $label;
    }

    /**
     * Get current PID (starting from TCA or FlexForm)
     */
    protected function getPageIdentifier(): int
    {
        if (!empty($this->params['row']['pid'])) {
            return (int)$this->params['row']['pid'];
        }

        if (!empty($this->params['flexParentDatabaseRow']['pid'])) {
            return (int)$this->params['flexParentDatabaseRow']['pid'];
        }

        return 0;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): AddOptionsToSelection
    {
        $this->type = $type;
        return $this;
    }
}
