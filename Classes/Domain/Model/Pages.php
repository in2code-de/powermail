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
class Tx_Powermail_Domain_Model_Pages extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title = '';

	/**
	 * css
	 *
	 * @var string
	 */
	protected $css = '';

	/**
	 * Powermail Fields
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_Powermail_Domain_Model_Fields>
	 */
	protected $fields = NULL;

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
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		/**
		 * Do not modify this method!
		 * It will be rewritten on each save in the extension builder
		 * You may modify the constructor of this class instead
		 */
		$this->fields = new Tx_Extbase_Persistence_ObjectStorage();
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
	 * Adds a Fields
	 *
	 * @param Tx_Powermail_Domain_Model_Fields $field
	 * @return void
	 */
	public function addField(Tx_Powermail_Domain_Model_Fields $field) {
		$this->fields->attach($field);
	}

	/**
	 * Removes a Fields
	 *
	 * @param Tx_Powermail_Domain_Model_Fields $fieldToRemove The Fields to be removed
	 * @return void
	 */
	public function removeField(Tx_Powermail_Domain_Model_Fields $fieldToRemove) {
		$this->fields->detach($fieldToRemove);
	}

	/**
	 * Returns the fields
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_Powermail_Domain_Model_Fields> $fields
	 */
	public function getFields() {
//		return $this->fields;

		// Workarround to get the fields sorted
		$fieldArray = array();
		foreach ($this->fields as $field) {
			$fieldArray[$field->getSorting()] = $field;
		}
		ksort($fieldArray);
		return $fieldArray;
	}

	/**
	 * Sets the fields
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_Powermail_Domain_Model_Fields> $fields
	 * @return void
	 */
	public function setFields(Tx_Extbase_Persistence_ObjectStorage $fields) {
		$this->fields = $fields;
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