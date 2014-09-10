<?php
namespace In2code\Powermail\Domain\Model;

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
 * PageModel
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class Page extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

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
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Powermail\Domain\Model\Field>
	 */
	protected $fields = NULL;

	/**
	 * Powermail Forms
	 *
	 * @var \In2code\Powermail\Domain\Model\Form
	 */
	protected $forms = NULL;

	/**
	 * sorting
	 *
	 * @var integer
	 */
	protected $sorting = 0;

	/**
	 * __construct
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->fields = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @return void
	 */
	public function addField(\In2code\Powermail\Domain\Model\Field $field) {
		$this->fields->attach($field);
	}

	/**
	 * Removes a Fields
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $fieldToRemove
	 * @return void
	 */
	public function removeField(\In2code\Powermail\Domain\Model\Field $fieldToRemove) {
		$this->fields->detach($fieldToRemove);
	}

	/**
	 * Returns the fields
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Sets the fields
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 * @return void
	 */
	public function setFields(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $fields) {
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

	/**
	 * @param \In2code\Powermail\Domain\Model\Form $forms
	 * @return void
	 */
	public function setForms($forms) {
		$this->forms = $forms;
	}

	/**
	 * @return \In2code\Powermail\Domain\Model\Form
	 */
	public function getForms() {
		return $this->forms;
	}

}