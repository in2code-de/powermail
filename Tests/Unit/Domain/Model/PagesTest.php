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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class Tx_Powermail_Domain_Model_Pages.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage powermail
 *
 * @author Alex Kellner <alexander.kellner@in2code.de>
 */
class Tx_Powermail_Domain_Model_PagesTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_Powermail_Domain_Model_Pages
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_Powermail_Domain_Model_Pages();
	}

	public function tearDown() {
		unset($this->fixture);
	}
	
	
	/**
	 * @test
	 */
	public function getTitleReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle() { 
		$this->fixture->setTitle('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getTitle()
		);
	}
	
	/**
	 * @test
	 */
	public function getCssReturnsInitialValueForInteger() { 
		$this->assertSame(
			0,
			$this->fixture->getCss()
		);
	}

	/**
	 * @test
	 */
	public function setCssForIntegerSetsCss() { 
		$this->fixture->setCss(12);

		$this->assertSame(
			12,
			$this->fixture->getCss()
		);
	}
	
	/**
	 * @test
	 */
	public function getFieldsReturnsInitialValueForObjectStorageContainingTx_Powermail_Domain_Model_Fields() { 
		$newObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->fixture->getFields()
		);
	}

	/**
	 * @test
	 */
	public function setFieldsForObjectStorageContainingTx_Powermail_Domain_Model_FieldsSetsFields() { 
		$field = new Tx_Powermail_Domain_Model_Fields();
		$objectStorageHoldingExactlyOneFields = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneFields->attach($field);
		$this->fixture->setFields($objectStorageHoldingExactlyOneFields);

		$this->assertSame(
			$objectStorageHoldingExactlyOneFields,
			$this->fixture->getFields()
		);
	}
	
	/**
	 * @test
	 */
	public function addFieldToObjectStorageHoldingFields() {
		$field = new Tx_Powermail_Domain_Model_Fields();
		$objectStorageHoldingExactlyOneField = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneField->attach($field);
		$this->fixture->addField($field);

		$this->assertEquals(
			$objectStorageHoldingExactlyOneField,
			$this->fixture->getFields()
		);
	}

	/**
	 * @test
	 */
	public function removeFieldFromObjectStorageHoldingFields() {
		$field = new Tx_Powermail_Domain_Model_Fields();
		$localObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$localObjectStorage->attach($field);
		$localObjectStorage->detach($field);
		$this->fixture->addField($field);
		$this->fixture->removeField($field);

		$this->assertEquals(
			$localObjectStorage,
			$this->fixture->getFields()
		);
	}
	
}
?>