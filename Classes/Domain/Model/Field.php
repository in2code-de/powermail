<?php
namespace In2code\Powermail\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use In2code\Powermail\Utility\Div;

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
 * Field Model
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class Field extends AbstractEntity {

	/**
	 * title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title = '';

	/**
	 * type
	 * 		Powermail basic field types are:
	 * 		"input", "textarea", "select", "check", "radio"
	 * 		"submit", "captcha", "reset", "text", "content"
	 * 		"html", "password", "file", "hidden", "date",
	 * 		"country", "location", "typoscript"
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $type = '';

	/**
	 * settings
	 *
	 * @var string
	 */
	protected $settings = '';

	/**
	 * $modifiedSettings
	 *
	 * @var string
	 */
	protected $modifiedSettings = '';

	/**
	 * path
	 *
	 * @var string
	 */
	protected $path = '';

	/**
	 * contentElement
	 *
	 * @var string
	 */
	protected $contentElement = '';

	/**
	 * text
	 *
	 * @var string
	 */
	protected $text = '';

	/**
	 * prefillValue
	 *
	 * @var string
	 */
	protected $prefillValue = '';

	/**
	 * placeholder
	 *
	 * @var string
	 */
	protected $placeholder = '';

	/**
	 * $createFromTyposcript
	 *
	 * @var string
	 */
	protected $createFromTyposcript = '';

	/**
	 * validation
	 *
	 * @var integer
	 */
	protected $validation = 0;

	/**
	 * validationConfiguration
	 *
	 * @var string
	 */
	protected $validationConfiguration = '';

	/**
	 * css
	 *
	 * @var string
	 */
	protected $css = '';

	/**
	 * description
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * multiselect
	 *
	 * @var bool
	 */
	protected $multiselect = FALSE;

	/**
	 * datepicker settings
	 *
	 * @var string
	 */
	protected $datepickerSettings = '';

	/**
	 * feuserValue
	 *
	 * @var string
	 */
	protected $feuserValue = '';

	/**
	 * senderName
	 *
	 * @var bool
	 */
	protected $senderName = FALSE;

	/**
	 * senderEmail
	 *
	 * @var bool
	 */
	protected $senderEmail = FALSE;

	/**
	 * mandatory
	 *
	 * @var boolean
	 */
	protected $mandatory = FALSE;

	/**
	 * marker
	 *
	 * @var string
	 */
	protected $marker = '';

	/**
	 * sorting
	 *
	 * @var integer
	 */
	protected $sorting = 0;

	/**
	 * pages
	 *
	 * @var \In2code\Powermail\Domain\Model\Page
	 */
	protected $pages = NULL;

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the type
	 *
	 * @return string $type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Sets the type
	 *
	 * @param string $type
	 * @return void
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * Check if this field is of a basic field type
	 * Basic field types are:
	 * 		"input", "textarea", "select", "check", "radio"
	 *
	 * @return bool
	 */
	public function isBasicFieldType() {
		$basicFieldTypes = array(
			'input',
			'textarea',
			'select',
			'check',
			'radio'
		);
		if (in_array($this->getType(), $basicFieldTypes)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Returns the settings
	 *
	 * @return string $settings
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * Sets the settings
	 *
	 * @param string $settings
	 * @return void
	 */
	public function setSettings($settings) {
		$this->settings = $settings;
	}

	/**
	 * Modify settings for select, radio and checkboxes
	 * 		option1 =>
	 * 			label => Red Shoes
	 * 			value => red
	 * 			selected => 1
	 *
	 * @return string
	 */
	public function getModifiedSettings() {
		return Div::optionArray(
			$this->getSettings(),
			$this->getCreateFromTyposcript()
		);
	}

	/**
	 * Returns the path
	 *
	 * @return string $path
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Sets the path
	 *
	 * @param string $path
	 * @return void
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * Returns the contentElement
	 *
	 * @return string $contentElement
	 */
	public function getContentElement() {
		return $this->contentElement;
	}

	/**
	 * Sets the contentElement
	 *
	 * @param string $contentElement
	 * @return void
	 */
	public function setContentElement($contentElement) {
		$this->contentElement = $contentElement;
	}

	/**
	 * Returns the text
	 *
	 * @return string $text
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * Sets the text
	 *
	 * @param string $text
	 * @return void
	 */
	public function setText($text) {
		$this->text = $text;
	}

	/**
	 * Returns the prefillValue
	 *
	 * @return string $prefillValue
	 */
	public function getPrefillValue() {
		return $this->prefillValue;
	}

	/**
	 * Sets the prefillValue
	 *
	 * @param string $prefillValue
	 * @return void
	 */
	public function setPrefillValue($prefillValue) {
		$this->prefillValue = $prefillValue;
	}

	/**
	 * @param string $placeholder
	 * @return void
	 */
	public function setPlaceholder($placeholder) {
		$this->placeholder = $placeholder;
	}

	/**
	 * @return string
	 */
	public function getPlaceholder() {
		return $this->placeholder;
	}

	/**
	 * @param string $createFromTyposcript
	 * @return void
	 */
	public function setCreateFromTyposcript($createFromTyposcript) {
		$this->createFromTyposcript = $createFromTyposcript;
	}

	/**
	 * @return string
	 */
	public function getCreateFromTyposcript() {
		return $this->createFromTyposcript;
	}

	/**
	 * Returns the validation
	 *
	 * @return integer $validation
	 */
	public function getValidation() {
		return $this->validation;
	}

	/**
	 * Sets the validation
	 *
	 * @param integer $validation
	 * @return void
	 */
	public function setValidation($validation) {
		$this->validation = $validation;
	}

	/**
	 * @param string $validationConfiguration
	 * @return void
	 */
	public function setValidationConfiguration($validationConfiguration) {
		$this->validationConfiguration = $validationConfiguration;
	}

	/**
	 * @return string
	 */
	public function getValidationConfiguration() {
		return $this->validationConfiguration;
	}

	/**
	 * Returns the css
	 *
	 * @return string $css
	 */
	public function getCss() {
		return $this->css;
	}

	/**
	 * Sets the css
	 *
	 * @param string $css
	 * @return void
	 */
	public function setCss($css) {
		$this->css = $css;
	}

	/**
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param boolean $multiselect
	 * @return void
	 */
	public function setMultiselect($multiselect) {
		$this->multiselect = $multiselect;
	}

	/**
	 * @return boolean
	 */
	public function getMultiselect() {
		return $this->multiselect;
	}

	/**
	 * @return string
	 */
	public function getMultiselectForField() {
		$value = $this->getMultiselect();
		if ($value) {
			$value = 'multiple';
		} else {
			$value = NULL;
		}
		return $value;
	}

	/**
	 * @param string $datepickerSettings
	 * @return void
	 */
	public function setDatepickerSettings($datepickerSettings) {
		$this->datepickerSettings = $datepickerSettings;
	}

	/**
	 * @return string
	 */
	public function getDatepickerSettings() {
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
	public function getDatepickerSettingsOptimized() {
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
	public function getFeuserValue() {
		return $this->feuserValue;
	}

	/**
	 * Sets the feuserValue
	 *
	 * @param string $feuserValue
	 * @return void
	 */
	public function setFeuserValue($feuserValue) {
		$this->feuserValue = $feuserValue;
	}

	/**
	 * Returns the senderEmail
	 *
	 * @return bool $senderEmail
	 */
	public function getSenderEmail() {
		return $this->senderEmail;
	}

	/**
	 * Sets the senderEmail
	 *
	 * @param bool $senderEmail
	 * @return void
	 */
	public function setSenderEmail($senderEmail) {
		$this->senderEmail = $senderEmail;
	}

	/**
	 * Returns the senderName
	 *
	 * @return bool $senderName
	 */
	public function getSenderName() {
		return $this->senderName;
	}

	/**
	 * Sets the senderName
	 *
	 * @param bool $senderName
	 * @return void
	 */
	public function setSenderName($senderName) {
		$this->senderName = $senderName;
	}

	/**
	 * Returns the mandatory
	 *
	 * @return boolean $mandatory
	 */
	public function getMandatory() {
		return $this->mandatory;
	}

	/**
	 * Sets the mandatory
	 *
	 * @param boolean $mandatory
	 * @return void
	 */
	public function setMandatory($mandatory) {
		$this->mandatory = $mandatory;
	}

	/**
	 * Returns the marker
	 *
	 * @return string $marker
	 */
	public function getMarker() {
		if (empty($this->marker)) {
			return 'uid' . $this->getUid();
		}
		return $this->marker;
	}

	/**
	 * Sets the marker
	 *
	 * @param string $marker
	 * @return void
	 */
	public function setMarker($marker) {
		$this->marker = $marker;
	}

	/**
	 * Returns the sorting
	 *
	 * @return integer $sorting
	 */
	public function getSorting() {
		return $this->sorting;
	}

	/**
	 * Sets the sorting
	 *
	 * @param integer $sorting
	 * @return void
	 */
	public function setSorting($sorting) {
		$this->sorting = $sorting;
	}

	/**
	 * @param \In2code\Powermail\Domain\Model\Page $pages
	 * @return void
	 */
	public function setPages($pages) {
		$this->pages = $pages;
	}

	/**
	 * @return \In2code\Powermail\Domain\Model\Page
	 */
	public function getPages() {
		return $this->pages;
	}

}