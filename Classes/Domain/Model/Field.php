<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Field
 */
class Field extends AbstractEntity
{
    const TABLE_NAME = 'tx_powermail_domain_model_field';
    const FIELD_TYPE_BASIC = 'basic';
    const FIELD_TYPE_ADVANCED = 'advanced';
    const FIELD_TYPE_EXTPORTABLE = 'exportable';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * type
     *        Powermail field types are:
     *        "input", "textarea", "select", "check", "radio"
     *        "submit", "captcha", "reset", "text", "content"
     *        "html", "password", "file", "hidden", "date",
     *        "country", "location", "typoscript"
     *
     * @var string
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $settings = '';

    /**
     * @var string
     */
    protected $modifiedSettings = '';

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var string
     */
    protected $contentElement = '';

    /**
     * @var string
     */
    protected $text = '';

    /**
     * @var string
     */
    protected $prefillValue = '';

    /**
     * @var string
     */
    protected $placeholder = '';

    /**
     * @var string
     */
    protected $createFromTyposcript = '';

    /**
     * @var integer
     */
    protected $validation = 0;

    /**
     * @var string
     */
    protected $validationConfiguration = '';

    /**
     * @var string
     */
    protected $css = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var bool
     */
    protected $multiselect = false;

    /**
     * @var string
     */
    protected $datepickerSettings = '';

    /**
     * @var string
     */
    protected $feuserValue = '';

    /**
     * @var bool
     */
    protected $senderName = false;

    /**
     * @var bool
     */
    protected $senderEmail = false;

    /**
     * @var boolean
     */
    protected $mandatory = false;

    /**
     * @var string
     */
    protected $marker = '';

    /**
     * @var integer
     */
    protected $sorting = 0;

    /**
     * @var integer
     */
    protected $l10nParent = 0;

    /**
     * @var \In2code\Powermail\Domain\Model\Page
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @extensionScannerIgnoreLine Still needed for TYPO3 8.7
     * @lazy
     */
    protected $pages = null;

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return TemplateUtility::fluidParseString($this->title);
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the type - must not be empty
     *
     * @return string $type
     */
    public function getType()
    {
        $type = $this->type;
        if (empty($type)) {
            $type = 'input';
            if ($this->isLocalized()) {
                $fieldRepository = ObjectUtility::getObjectManager()->get(FieldRepository::class);
                $originalType = $fieldRepository->getTypeFromUid($this->getUid());
                if (!empty($originalType)) {
                    $type = $originalType;
                }
            }
        }
        return $type;
    }

    /**
     * Sets the type
     *
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Check if this field is of a basic field type
     * Basic field types are:
     *        "input", "textarea", "select", "check", "radio"
     *
     * @return bool
     */
    public function isBasicFieldType()
    {
        $basicFieldTypes = [
            'input',
            'textarea',
            'select',
            'check',
            'radio'
        ];
        return in_array($this->getType(), $basicFieldTypes);
    }

    /**
     * Check if this field is of an advanced field type (includes also basic field types)
     * basicly used for export and frontend editing
     *
     * @return bool
     */
    public function isAdvancedFieldType()
    {
        $advancedFieldTypes = [
            'hidden',
            'file',
            'location',
            'date',
            'country',
            'password'
        ];
        return $this->isBasicFieldType() || in_array($this->getType(), $advancedFieldTypes);
    }

    /**
     * Check if this field is exportable
     *
     * @return bool
     */
    public function isExportableFieldType()
    {
        return $this->isAdvancedFieldType() || in_array($this->getType(), $this->getExportableTypesFromTypoScript());
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isTypeOf($type)
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

    /**
     * Returns the settings
     *
     * @return string $settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Sets the settings
     *
     * @param string $settings
     * @return void
     */
    public function setSettings($settings)
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
     * @return array
     */
    public function getModifiedSettings()
    {
        return $this->optionArray($this->getSettings(), $this->getCreateFromTyposcript());
    }

    /**
     * Returns the path
     *
     * @return string $path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path
     *
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Returns the contentElement
     *
     * @return string $contentElement
     */
    public function getContentElement()
    {
        return $this->contentElement;
    }

    /**
     * Sets the contentElement
     *
     * @param string $contentElement
     * @return void
     */
    public function setContentElement($contentElement)
    {
        $this->contentElement = $contentElement;
    }

    /**
     * Returns the text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets the text
     *
     * @param string $text
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Returns the prefillValue
     *
     * @return string $prefillValue
     */
    public function getPrefillValue()
    {
        return $this->prefillValue;
    }

    /**
     * Sets the prefillValue
     *
     * @param string $prefillValue
     * @return void
     */
    public function setPrefillValue($prefillValue)
    {
        $this->prefillValue = $prefillValue;
    }

    /**
     * @param string $placeholder
     * @return void
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param string $createFromTyposcript
     * @return void
     */
    public function setCreateFromTyposcript($createFromTyposcript)
    {
        $this->createFromTyposcript = $createFromTyposcript;
    }

    /**
     * @return string
     */
    public function getCreateFromTyposcript()
    {
        return $this->createFromTyposcript;
    }

    /**
     * Returns the validation
     *
     * @return integer $validation
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * Sets the validation
     *
     * @param integer $validation
     * @return void
     */
    public function setValidation($validation)
    {
        $this->validation = $validation;
    }

    /**
     * @param string $validationConfiguration
     * @return void
     */
    public function setValidationConfiguration($validationConfiguration)
    {
        $this->validationConfiguration = $validationConfiguration;
    }

    /**
     * @return string
     */
    public function getValidationConfiguration()
    {
        return $this->validationConfiguration;
    }

    /**
     * Returns the css
     *
     * @return string $css
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Sets the css
     *
     * @param string $css
     * @return void
     */
    public function setCss($css)
    {
        $this->css = $css;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param boolean $multiselect
     * @return void
     */
    public function setMultiselect($multiselect)
    {
        $this->multiselect = $multiselect;
    }

    /**
     * @return boolean
     */
    public function isMultiselect()
    {
        return $this->multiselect;
    }

    /**
     * @return string
     */
    public function getMultiselectForField()
    {
        $value = $this->isMultiselect();
        if ($value) {
            $value = 'multiple';
        } else {
            $value = null;
        }
        return $value;
    }

    /**
     * @param string $datepickerSettings
     * @return void
     */
    public function setDatepickerSettings($datepickerSettings)
    {
        $this->datepickerSettings = $datepickerSettings;
    }

    /**
     * @return string
     */
    public function getDatepickerSettings()
    {
        $datepickerSettings = $this->datepickerSettings;
        if (empty($datepickerSettings)) {
            $datepickerSettings = 'date';
        }
        return $datepickerSettings;
    }

    /**
     * Rewrite datetime to datetime-local (Chrome support)
     *
     * @return string
     */
    public function getDatepickerSettingsOptimized()
    {
        $settings = $this->getDatepickerSettings();
        if ($settings === 'datetime') {
            $settings = 'datetime-local';
        }
        return $settings;
    }

    /**
     * Returns the feuserValue
     *
     * @return string $feuserValue
     */
    public function getFeuserValue()
    {
        return $this->feuserValue;
    }

    /**
     * Sets the feuserValue
     *
     * @param string $feuserValue
     * @return void
     */
    public function setFeuserValue($feuserValue)
    {
        $this->feuserValue = $feuserValue;
    }

    /**
     * Returns the senderEmail
     *
     * @return bool $senderEmail
     */
    public function isSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * Sets the senderEmail
     *
     * @param bool $senderEmail
     * @return void
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * Returns the senderName
     *
     * @return bool $senderName
     */
    public function isSenderName()
    {
        return $this->senderName;
    }

    /**
     * Sets the senderName
     *
     * @param bool $senderName
     * @return void
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * Returns the mandatory
     *
     * @return boolean $mandatory
     */
    public function isMandatory()
    {
        return $this->mandatory;
    }

    /**
     * Sets the mandatory
     *
     * @param boolean $mandatory
     * @return void
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;
    }

    /**
     * Returns the marker
     *
     * @return string $marker
     */
    public function getMarker()
    {
        $marker = $this->marker;
        if ($this->isLocalized()) {
            $fieldRepository = ObjectUtility::getObjectManager()->get(FieldRepository::class);
            $marker = $fieldRepository->getMarkerFromUid($this->getUid());
        }
        if (empty($marker)) {
            $marker = 'uid' . $this->getUid();
        }
        return $marker;
    }

    /**
     * Returns the marker, even if empty
     *
     * @return string $marker
     */
    public function getMarkerOriginal()
    {
        return $this->marker;
    }

    /**
     * Sets the marker
     *
     * @param string $marker
     * @return void
     */
    public function setMarker($marker)
    {
        $this->marker = $marker;
    }

    /**
     * Returns the sorting
     *
     * @return integer $sorting
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Sets the sorting
     *
     * @param integer $sorting
     * @return void
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @param \In2code\Powermail\Domain\Model\Page $pages
     * @return void
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    /**
     * @return \In2code\Powermail\Domain\Model\Page
     */
    public function getPages()
    {
        return $this->pages;
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
     * @param bool $parse
     * @return array Options Array
     */
    protected function optionArray($string, $typoScriptObjectPath, $parse = true)
    {
        if (empty($string)) {
            $string = TypoScriptUtility::parseTypoScriptFromTypoScriptPath($typoScriptObjectPath);
        }
        if (empty($string)) {
            $string = 'Error, no options to show';
        }
        return $this->buildOptions($string, $parse);
    }

    /**
     * @param string $string Options from the Textarea
     * @param $parse
     * @return array
     */
    protected function buildOptions($string, $parse)
    {
        $options = [];
        $string = str_replace('[\n]', PHP_EOL, $string);
        $settingsField = GeneralUtility::trimExplode(PHP_EOL, $string, true);
        foreach ($settingsField as $line) {
            $settings = GeneralUtility::trimExplode('|', $line, false);
            $value = (isset($settings[1]) ? $settings[1] : $settings[0]);
            $label = ($parse ? TemplateUtility::fluidParseString($settings[0]) : $settings[0]);
            $options[] = [
                'label' => $label,
                'value' => $value,
                'selected' => isset($settings[2]) ? 1 : 0
            ];
        }
        return $options;
    }

    /**
     * Return expected value type from fieldtype
     *
     * @param string $fieldType
     * @return int
     */
    public function dataTypeFromFieldType($fieldType)
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
                'password' => Answer::VALUE_TYPE_TEXT,
                'radio' => Answer::VALUE_TYPE_TEXT,
                'reset' => Answer::VALUE_TYPE_TEXT,
                'select' => Answer::VALUE_TYPE_TEXT,
                'submit' => Answer::VALUE_TYPE_TEXT,
                'text' => Answer::VALUE_TYPE_TEXT,
                'textarea' => Answer::VALUE_TYPE_TEXT,
                'typoscript' => Answer::VALUE_TYPE_TEXT
            ];
            $types = $this->extendTypeArrayWithTypoScriptTypes($types);
        }

        // change select fieldtype to array if multiple checked
        if ($fieldType === 'select') {
            $types['select'] = $this->isMultiselect() ? 1 : 0;
        }

        if (array_key_exists($fieldType, $types)) {
            $dataType = $types[$fieldType];
        }
        return $dataType;
    }

    /**
     * @return bool
     */
    public function isLocalized()
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
     * @param array $types
     * @return array
     */
    protected function extendTypeArrayWithTypoScriptTypes(array $types)
    {
        $typoScript = BackendUtility::getPagesTSconfig(FrontendUtility::getCurrentPageIdentifier());
        $configuration = $typoScript['tx_powermail.']['flexForm.'];
        foreach ((array)$configuration['type.']['addFieldOptions.'] as $fieldTypeName => $fieldType) {
            if (!empty($fieldType['dataType'])) {
                $fieldTypeName = substr($fieldTypeName, 0, -1);
                $types[$fieldTypeName] = (int)$fieldType['dataType'];
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
     */
    protected function getExportableTypesFromTypoScript()
    {
        $types = [];
        $typoScript = BackendUtility::getPagesTSconfig($this->getPid());
        $configuration = $typoScript['tx_powermail.']['flexForm.'];
        foreach ((array)$configuration['type.']['addFieldOptions.'] as $fieldTypeName => $fieldType) {
            if (!empty($fieldType['export'])) {
                if ($fieldType['export'] === '1') {
                    $fieldTypeName = rtrim($fieldTypeName, '.');
                    $types[] = $fieldTypeName;
                }
            }
        }
        return $types;
    }
}
