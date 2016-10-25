<?php
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class Field
 * @package In2code\Powermail\Domain\Model
 */
class Field extends AbstractEntity
{

    const TABLE_NAME = 'tx_powermail_domain_model_field';

    /**
     * @var string
     * @validate NotEmpty
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
     * @validate NotEmpty
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
     * @var \In2code\Powermail\Domain\Model\Page
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
     * Returns the type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
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
        if (empty($this->marker)) {
            return 'uid' . $this->getUid();
        }
        return $this->marker;
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
        static $types = null;
        if (is_null($types)) {
            $types = [
                'captcha' => 0,
                'check' => 1,
                'content' => 0,
                'date' => 2,
                'file' => 3,
                'hidden' => 0,
                'html' => 0,
                'input' => 0,
                'location' => 0,
                'password' => 0,
                'radio' => 0,
                'reset' => 0,
                'select' => 0,
                'submit' => 0,
                'text' => 0,
                'textarea' => 0,
                'typoscript' => 0
            ];
            $types = $this->extendTypeArrayWithTypoScriptTypes($types);
        }

        // change select fieldtype to array if multiple checked
        if ($fieldType === 'select' && $this->isMultiselect()) {
            $types['select'] = 1;
        }

        if (array_key_exists($fieldType, $types)) {
            return $types[$fieldType];
        }
        return 0;
    }

    /**
     * Extend dataType with TSConfig
     *
     * @param array $types
     * @return array
     */
    protected function extendTypeArrayWithTypoScriptTypes(array $types)
    {
        $typoScript = BackendUtility::getPagesTSconfig(FrontendUtility::getCurrentPageIdentifier());
        $configuration = $typoScript['tx_powermail.']['flexForm.'];
        foreach ($configuration['type.']['addFieldOptions.'] as $fieldTypeName => $fieldType) {
            if (!empty($fieldType['dataType'])) {
                $fieldTypeName = substr($fieldTypeName, 0, -2);
                $types[$fieldTypeName] = (int) $fieldType['dataType'];
            }
        }
        return $types;
    }
}
