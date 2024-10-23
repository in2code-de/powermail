<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\LocalizationUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;

/**
 * Class Field
 */
class Field extends AbstractEntity
{
    const TABLE_NAME = 'tx_powermail_domain_model_field';

    const FIELD_TYPE_BASIC = 'basic';

    const FIELD_TYPE_ADVANCED = 'advanced';

    const FIELD_TYPE_EXTPORTABLE = 'exportable';

    protected string $title = '';

    /**
     * type
     *        Powermail field types are:
     *        "input", "textarea", "select", "check", "radio"
     *        "submit", "captcha", "reset", "text", "content"
     *        "html", "password", "file", "hidden", "date",
     *        "country", "location", "typoscript"
     */
    protected string $type = '';

    protected string $settings = '';

    protected string $path = '';

    protected int $contentElement = 0;

    protected string $text = '';

    protected string $prefillValue = '';

    protected string $placeholder = '';

    protected string $createFromTyposcript = '';

    protected int $validation = 0;

    protected string $validationConfiguration = '';

    protected string $css = '';

    protected string $description = '';

    protected bool $multiselect = false;

    protected string $datepickerSettings = '';

    protected string $feuserValue = '';

    protected bool $senderName = false;

    protected bool $senderEmail = false;

    protected bool $mandatory = false;

    protected string $marker = '';

    protected int $sorting = 0;

    protected int $l10nParent = 0;

    /**
     * @var Page
     * This property can hold Page|int|null (depending on the context). "@var" must set to Page for property mapping.
     */
    protected $page;

    public function getTitle(): string
    {
        return TemplateUtility::fluidParseString($this->title);
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Returns the type - must not be empty
     *
     * @return string $type
     * @throws DBALException
     */
    public function getType(): string
    {
        $type = $this->type;
        if ($type === '' || $type === '0') {
            $type = 'input';
            if ($this->isLocalized()) {
                $fieldRepository = GeneralUtility::makeInstance(FieldRepository::class);
                $originalType = $fieldRepository->getTypeFromUid($this->getUid());
                if (!empty($originalType)) {
                    $type = $originalType;
                }
            }
        }

        return $type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Check if this field is of a basic field type
     * Basic field types are:
     *        "input", "textarea", "select", "check", "radio"
     *
     * @throws DBALException
     */
    public function isBasicFieldType(): bool
    {
        $basicFieldTypes = [
            'input',
            'textarea',
            'select',
            'check',
            'radio',
        ];
        return in_array($this->getType(), $basicFieldTypes);
    }

    /**
     * Check if this field is of an advanced field type (includes also basic field types)
     * basicly used for export and frontend editing
     *
     * @throws DBALException
     */
    public function isAdvancedFieldType(): bool
    {
        $advancedFieldTypes = [
            'hidden',
            'file',
            'location',
            'date',
            'country',
            'password',
        ];
        if ($this->isBasicFieldType()) {
            return true;
        }
        return in_array($this->getType(), $advancedFieldTypes);
    }

    /**
     * @throws DeprecatedException
     * @throws DBALException
     */
    public function isExportableFieldType(): bool
    {
        if ($this->isAdvancedFieldType()) {
            return true;
        }
        return in_array($this->getType(), $this->getExportableTypesFromTypoScript());
    }

    /**
     * @throws DeprecatedException
     * @throws DBALException
     */
    public function isTypeOf(string $type): bool
    {
        if ($type === self::FIELD_TYPE_BASIC) {
            return $this->isBasicFieldType();
        }

        if ($type === self::FIELD_TYPE_ADVANCED) {
            return $this->isAdvancedFieldType();
        }

        if ($type === self::FIELD_TYPE_EXTPORTABLE) {
            return $this->isExportableFieldType();
        }

        return false;
    }

    public function getSettings(): string
    {
        return $this->settings;
    }

    public function setSettings(string $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * Modify settings for select, radio and checkboxes
     *        option1 =>
     *            label => Red Shoes
     *            value => red
     *            selected => 1
     *
     * @throws ExceptionExtbaseObject
     */
    public function getModifiedSettings(): array
    {
        return $this->optionArray($this->getSettings(), $this->getCreateFromTyposcript());
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getContentElement(): int
    {
        return $this->contentElement;
    }

    public function setContentElement(int $contentElement): void
    {
        $this->contentElement = $contentElement;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getPrefillValue(): string
    {
        return $this->prefillValue;
    }

    public function setPrefillValue(string $prefillValue): void
    {
        $this->prefillValue = $prefillValue;
    }

    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    public function setCreateFromTyposcript(string $createFromTyposcript): void
    {
        $this->createFromTyposcript = $createFromTyposcript;
    }

    public function getCreateFromTyposcript(): string
    {
        return $this->createFromTyposcript;
    }

    public function getValidation(): int
    {
        return $this->validation;
    }

    public function setValidation(int $validation): void
    {
        $this->validation = $validation;
    }

    public function setValidationConfiguration(string $validationConfiguration): void
    {
        $this->validationConfiguration = $validationConfiguration;
    }

    public function getValidationConfiguration(): string
    {
        return $this->validationConfiguration;
    }

    public function getCss(): string
    {
        return $this->css;
    }

    public function setCss(string $css): void
    {
        $this->css = $css;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setMultiselect(bool $multiselect): void
    {
        $this->multiselect = $multiselect;
    }

    public function isMultiselect(): bool
    {
        return $this->multiselect;
    }

    public function getMultiselectForField(): string
    {
        $value = $this->isMultiselect();
        if ($value) {
            return 'multiple';
        }

        return '';
    }

    public function setDatepickerSettings(string $datepickerSettings): void
    {
        $this->datepickerSettings = $datepickerSettings;
    }

    public function getDatepickerSettings(): string
    {
        $datepickerSettings = $this->datepickerSettings;
        if ($datepickerSettings === '' || $datepickerSettings === '0') {
            return 'date';
        }

        return $datepickerSettings;
    }

    /**
     * Rewrite datetime to datetime-local (Chrome support)
     */
    public function getDatepickerSettingsOptimized(): string
    {
        $settings = $this->getDatepickerSettings();
        if ($settings === 'datetime') {
            return 'datetime-local';
        }

        return $settings;
    }

    public function getFeuserValue(): string
    {
        return $this->feuserValue;
    }

    public function setFeuserValue(string $feuserValue): void
    {
        $this->feuserValue = $feuserValue;
    }

    public function isSenderEmail(): bool
    {
        return $this->senderEmail;
    }

    public function setSenderEmail(bool $senderEmail): void
    {
        $this->senderEmail = $senderEmail;
    }

    public function isSenderName(): bool
    {
        return $this->senderName;
    }

    public function setSenderName(bool $senderName): void
    {
        $this->senderName = $senderName;
    }

    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    public function setMandatory(bool $mandatory): void
    {
        $this->mandatory = $mandatory;
    }

    /**
     * @return string $marker
     * @throws DBALException
     */
    public function getMarker(): string
    {
        $marker = $this->marker;
        if ($this->isLocalized()) {
            $fieldRepository = GeneralUtility::makeInstance(FieldRepository::class);
            $marker = $fieldRepository->getMarkerFromUid($this->getUid());
        }

        if (empty($marker)) {
            return 'uid' . $this->getUid();
        }

        return $marker;
    }

    /**
     * Returns the marker, even if empty
     *
     * @return string $marker
     */
    public function getMarkerOriginal(): string
    {
        return $this->marker;
    }

    public function setMarker(string $marker): void
    {
        $this->marker = $marker;
    }

    public function getSorting(): int
    {
        return $this->sorting;
    }

    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }

    public function setPage(Page $page): void
    {
        $this->page = $page;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    /**
     * Create an options array (Needed for fieldsettings: select, radio, check)
     *        option1 =>
     *            label => Red Shoes
     *            value => red
     *            selected => 1
     *
     * @param string $string Options from the Textarea
     * @param string $typoScriptObjectPath Path to TypoScript like lib.blabla
     * @return array Options Array
     * @throws ExceptionExtbaseObject
     */
    protected function optionArray(string $string, string $typoScriptObjectPath, bool $parse = true): array
    {
        if ($string === '' || $string === '0') {
            $string = TypoScriptUtility::parseTypoScriptFromTypoScriptPath($typoScriptObjectPath);
        }

        if ($string === '' || $string === '0') {
            $string = LocalizationUtility::translate('selectErrorEmpty');
        }

        return $this->buildOptions($string, $parse);
    }

    /**
     * @param string $string Options from the Textarea
     */
    protected function buildOptions(string $string, bool $parse): array
    {
        $options = [];
        $string = str_replace('[\n]', PHP_EOL, $string);
        $settingsField = GeneralUtility::trimExplode(PHP_EOL, $string, true);
        foreach ($settingsField as $line) {
            $settings = GeneralUtility::trimExplode('|', $line, false);
            $value = ($settings[1] ?? $settings[0]);
            $label = ($parse ? TemplateUtility::fluidParseString($settings[0]) : $settings[0]);
            $options[] = [
                'label' => $label,
                'value' => $value,
                'selected' => !empty($settings[2]) && $settings[2] === '*' ? 1 : 0,
            ];
        }

        return $options;
    }

    /**
     * Return expected value type from fieldtype
     *
     * @throws DeprecatedException
     */
    public function dataTypeFromFieldType(string $fieldType): int
    {
        $dataType = 0;
        static $types = null;
        if (is_null($types)) {
            $types = [
                'captcha' => Answer::VALUE_TYPE_TEXT,
                'check' => Answer::VALUE_TYPE_ARRAY,
                'content' => Answer::VALUE_TYPE_TEXT,
                'date' => Answer::VALUE_TYPE_DATE,
                'file' => Answer::VALUE_TYPE_UPLOAD,
                'hidden' => Answer::VALUE_TYPE_TEXT,
                'html' => Answer::VALUE_TYPE_TEXT,
                'input' => Answer::VALUE_TYPE_TEXT,
                'location' => Answer::VALUE_TYPE_TEXT,
                'password' => Answer::VALUE_TYPE_PASSWORD,
                'radio' => Answer::VALUE_TYPE_TEXT,
                'reset' => Answer::VALUE_TYPE_TEXT,
                'select' => Answer::VALUE_TYPE_TEXT,
                'submit' => Answer::VALUE_TYPE_TEXT,
                'text' => Answer::VALUE_TYPE_TEXT,
                'textarea' => Answer::VALUE_TYPE_TEXT,
                'typoscript' => Answer::VALUE_TYPE_TEXT,
            ];
            $types = $this->extendTypeArrayWithTypoScriptTypes($types);
        }

        // change select fieldtype to array if multiple checked
        if ($fieldType === 'select') {
            $types['select'] = $this->isMultiselect() ? 1 : 0;
        }
        if (array_key_exists($fieldType, $types)) {
            return $types[$fieldType];
        }

        return $dataType;
    }

    public function isLocalized(): bool
    {
        return $this->_getProperty('_languageUid') > 0 &&
            $this->_getProperty('l10nParent') > 0;
    }

    /**
     * Extend dataType with TSConfig
     *
     *      Example Page TSConfig:
     *          tx_powermail.flexForm.type.addFieldOptions.new = New Field
     *          tx_powermail.flexForm.type.addFieldOptions.new.dataType = 0
     *
     * @throws DeprecatedException
     */
    protected function extendTypeArrayWithTypoScriptTypes(array $types): array
    {
        $typoScript = BackendUtility::getPagesTSconfig(FrontendUtility::getCurrentPageIdentifier());
        if (!empty($typoScript['tx_powermail.']['flexForm.'])) {
            $configuration = $typoScript['tx_powermail.']['flexForm.'];
            if (isset($configuration['type.']['addFieldOptions.'])) {
                foreach ((array)$configuration['type.']['addFieldOptions.'] as $fieldTypeName => $fieldType) {
                    if (!empty($fieldType['dataType'])) {
                        $fieldTypeName = substr($fieldTypeName, 0, -1);
                        $types[$fieldTypeName] = (int)$fieldType['dataType'];
                    }
                }
            }
        }

        return $types;
    }

    /**
     * Extend exportable field type with types from TSConfig
     *
     *      Example Page TSConfig:
     *          tx_powermail.flexForm.type.addFieldOptions.new = New Field
     *          tx_powermail.flexForm.type.addFieldOptions.new.export = 1
     *
     * @return array ['new', 'myownfield']
     * @throws DeprecatedException
     */
    protected function getExportableTypesFromTypoScript(): array
    {
        $types = [];
        $typoScript = BackendUtility::getPagesTSconfig($this->getPid());
        if (ArrayUtility::isValidPath($typoScript, 'tx_powermail./flexForm.')) {
            $configuration = $typoScript['tx_powermail.']['flexForm.'];
            $configuration['type.'] ??= [];
            foreach ((array)($configuration['type.']['addFieldOptions.'] ?? []) as $fieldTypeName => $fieldType) {
                if (empty($fieldType['export'])) {
                    continue;
                }
                if ($fieldType['export'] !== '1') {
                    continue;
                }
                $fieldTypeName = rtrim($fieldTypeName, '.');
                $types[] = $fieldTypeName;
            }
        }

        return $types;
    }
}
