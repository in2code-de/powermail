<?php
namespace In2code\Powermail\Tests\Utility\Hook;

use TYPO3\CMS\Core\Tests\UnitTestCase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * CreateMarker Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class CreateMarkerTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Utility\Hook\CreateMarker
	 */
	protected $createMarkerMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->createMarkerMock = $this->getAccessibleMock(
			'\In2code\Powermail\Utility\Hook\CreateMarker',
			array('dummy'),
			array(TRUE)
		);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->createMarkerMock);
	}

	/**
	 * Dataprovider cleanStringReturnsString()
	 *
	 * @return array
	 */
	public function cleanStringReturnsStringDataProvider() {
		return array(
			array(
				'test',
				'default',
				'test',
			),
			array(
				'This is A Test',
				'default',
				'thisisatest',
			),
			array(
				'$T h%ißs_-',
				'default',
				'this__',
			),
			array(
				'$ %ß#',
				'default',
				'default',
			),
		);
	}

	/**
	 * Test for cleanString()
	 *
	 * @param string $string
	 * @param string $defaultValue
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider cleanStringReturnsStringDataProvider
	 * @test
	 */
	public function cleanStringReturnsString($string, $defaultValue, $expectedResult) {
		$this->assertSame($expectedResult, $this->createMarkerMock->_callRef('cleanString', $string, $defaultValue));
	}

	/**
	 * Dataprovider makeUniqueValueInArrayReturnsVoid()
	 *
	 * @return array
	 */
	public function makeUniqueValueInArrayReturnsVoidDataProvider() {
		return array(
			array(
				array(
					'abc'
				),
				array(
					'abc'
				)
			),
			array(
				array(
					'abc',
					'abc'
				),
				array(
					'abc',
					'abc_01'
				)
			),
			array(
				array(
					'abc',
					'abc_01',
					'abc_02',
				),
				array(
					'abc',
					'abc_01',
					'abc_02',
				)
			),
			array(
				array(
					'abc_01',
					'abc_01',
					'xxx',
				),
				array(
					'abc_01',
					'abc_02',
					'xxx',
				)
			),
		);
	}

	/**
	 * Test for makeUniqueValueInArray()
	 *
	 * @param array $array
	 * @param array $expectedResult
	 * @return void
	 * @dataProvider makeUniqueValueInArrayReturnsVoidDataProvider
	 * @test
	 */
	public function makeUniqueValueInArrayReturnsVoid($array, $expectedResult) {
		$this->createMarkerMock->_callRef('makeUniqueValueInArray', $array);
		$this->assertSame($expectedResult, $array);
	}
}
