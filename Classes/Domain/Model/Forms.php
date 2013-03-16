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
class Tx_Powermail_Domain_Model_Forms extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title ='';

	/**
	 * css
	 *
	 * @var string
	 */
	protected $css ='';

	/**
	 * pages
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_Powermail_Domain_Model_Pages>
	 */
	protected $pages = NULL;

	/**
	 * formsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FormsRepository
	 */
	protected $formsRepository = NULL;

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
	 * Returns the pages
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_Powermail_Domain_Model_Pages> $pages
	 */
	public function getPages() {
		// if elementbrowser instead of IRRE (sorting workarround)
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
		if ($confArr['replaceIrreWithElementBrowser']) {
			$formSorting = t3lib_div::trimExplode(',', $this->formsRepository->getPagesValue($this->uid), 1);
			$formSorting = array_flip($formSorting);
			$pageArray = array();
			foreach ($this->pages as $page) {
				$pageArray[$formSorting[$page->getUid()]] = $page;
			}
			ksort($pageArray);
			return $pageArray;
		}

		return $this->pages;
	}

	/**
	 * Sets the pages
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_Powermail_Domain_Model_Pages> $pages
	 * @return void
	 */
	public function setPages(Tx_Extbase_Persistence_ObjectStorage $pages) {
		$this->pages = $pages;
	}

	/**
	 * injectFormsRepository
	 *
	 * @param Tx_Powermail_Domain_Repository_FormsRepository $formsRepository
	 * @return void
	 */
	public function injectFormsRepository(Tx_Powermail_Domain_Repository_FormsRepository $formsRepository) {
		$this->formsRepository = $formsRepository;
	}

}
?>