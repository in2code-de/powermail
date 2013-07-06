<?php

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
 *
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Powermail_Domain_Model_Fields extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title = '';

	/**
	 * type
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
	 * validation
	 *
	 * @var integer
	 */
	protected $validation = 0;

	/**
	 * css
	 *
	 * @var string
	 */
	protected $css = '';

	/**
	 * feuserValue
	 *
	 * @var string
	 */
	protected $feuserValue = '';

	/**
	 * senderName
	 *
	 * @var string
	 */
	protected $senderName = '';

	/**
	 * senderEmail
	 *
	 * @var string
	 */
	protected $senderEmail = '';

	/**
	 * mandatory
	 *
	 * @var boolean
	 */
	protected $mandatory = false;

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
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {

	}

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
	 * Returns the settings
	 * 		option1 =>
	 * 			label => Red Shoes
	 * 			value => red
	 * 			selected => 1
	 *
	 * @return string $settings
	 */
	public function getSettings() {
		return Tx_Powermail_Utility_Div::optionArray($this->settings);
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
	 * @return string $senderEmail
	 */
	public function getSenderEmail() {
		return $this->senderEmail;
	}

	/**
	 * Sets the senderEmail
	 *
	 * @param string $senderEmail
	 * @return void
	 */
	public function setSenderEmail($senderEmail) {
		$this->senderEmail = $senderEmail;
	}

	/**
	 * Returns the senderName
	 *
	 * @return string $senderName
	 */
	public function getSenderName() {
		return $this->senderName;
	}

	/**
	 * Sets the senderName
	 *
	 * @param string $senderName
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

}
?>