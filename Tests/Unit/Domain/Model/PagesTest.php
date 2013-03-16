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
	public function getCssReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->fixture->getCss()
		);
	}

	/**
	 * @test
	 */
	public function setCssForStringSetsCss() {
		$this->fixture->setCss('my CSS');

		$this->assertSame(
			'my CSS',
			$this->fixture->getCss()
		);
	}
	
	/**
	 * @test
	 */
	public function getFieldsReturnsInitialValueForObjectStorageContainingTx_Powermail_Domain_Model_Fields() { 
		#$newObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$this->assertSame(
			array(),
			$this->fixture->getFields()
		);
	}

	/**
	 * @test
	 */
	public function setFieldsForObjectStorageContainingTx_Powermail_Domain_Model_FieldsSetsFields() { 
		$objectStorage = new Tx_Extbase_Persistence_ObjectStorage();

        $field = new Tx_Powermail_Domain_Model_Fields();
        $field->setSorting(42);
        $objectStorage->attach($field);

		$this->fixture->setFields($objectStorage);

		$this->assertSame(
            array(
                42 => $field,
            ),
            $this->fixture->getFields()
		);
	}
	
	/**
	 * @test
	 */
	public function addFieldToObjectStorageHoldingFields() {
		$field = new Tx_Powermail_Domain_Model_Fields();
		$field->setSorting(42);
        $this->fixture->addField($field);

		$this->assertSame(
            array(
                    42 => $field,
            ),
            $this->fixture->getFields()
		);
	}

	/**
	 * @test
	 */
	public function removeFieldFromObjectStorageHoldingFields() {
        $field = new Tx_Powermail_Domain_Model_Fields();
        $field->setSorting(42);
        $this->fixture->addField($field);

        $this->assertSame(
            array(
                42 => $field,
            ),
            $this->fixture->getFields()
        );
        $this->fixture->removeField($field);

        $this->assertSame(
            array(),
            $this->fixture->getFields()
        );
	}
	
}
?>