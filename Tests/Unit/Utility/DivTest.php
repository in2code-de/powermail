<?php
namespace In2code\Powermail\Tests\Utility;

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
 * Div Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class DivTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @var \In2code\Powermail\Utility\Div
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock('\In2code\Powermail\Utility\Div', array('dummy'));
		$oM = new \TYPO3\CMS\Extbase\Object\ObjectManager;
		$objectManager = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManagerInterface');
		$this->generalValidatorMock->_set('objectManager', $objectManager);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->generalValidatorMock);
	}

	/**
	 * Test for getStoragePage()
	 *
	 * @return void
	 * @test
	 */
	public function getStoragePageReturnsInt() {
		$result = \In2code\Powermail\Utility\Div::getStoragePage(123);
		$this->assertSame($result, 123);
	}

	/**
	 * Dataprovider getDataTypeFromFieldTypeReturnsInt()
	 *
	 * @return array
	 */
	public function getDataTypeFromFieldTypeReturnsIntDataProvider() {
		return array(
			array(
				'captcha',
				0
			),
			array(
				'check',
				1
			),
			array(
				'date',
				2
			),
			array(
				'file',
				3
			),
			array(
				'select',
				1
			),
			array(
				'input',
				0
			)
		);
	}

	/**
	 * Test for getDataTypeFromFieldType()
	 *
	 * @param \string $value
	 * @param \int $expectedResult
	 * @return void
	 * @dataProvider getDataTypeFromFieldTypeReturnsIntDataProvider
	 * @test
	 */
	public function getDataTypeFromFieldTypeReturnsInt($value, $expectedResult) {
		$result = \In2code\Powermail\Utility\Div::getDataTypeFromFieldType($value);
		$this->assertSame($result, $expectedResult);
	}

	/**
	 * Test for createHash()
	 *
	 * @return void
	 * @test
	 */
	public function createHashReturnsString() {
		$value = 'abc';
		$result = $this->generalValidatorMock->_callRef('createHash', $value);
		$this->assertFalse(($value == $result));
	}

	/**
	 * Dataprovider getSubFolderOfCurrentUrlReturnsString()
	 *
	 * @return array
	 */
	public function getSubFolderOfCurrentUrlReturnsStringDataProvider() {
		return array(
			array(
				TRUE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				'/'
			),
			array(
				FALSE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				'/'
			),
			array(
				TRUE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				'/'
			),
			array(
				FALSE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				''
			),
			array(
				TRUE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'/subfolder/'
			),
			array(
				FALSE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'subfolder/'
			),
			array(
				TRUE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'/subfolder'
			),
			array(
				FALSE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'subfolder'
			),
		);
	}

	/**
	 * Test for getSubFolderOfCurrentUrl()
	 *
	 * @param bool $leadingSlash will be prepended
	 * @param bool $trailingSlash will be appended
	 * @param string $host
	 * @param string $url
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider getSubFolderOfCurrentUrlReturnsStringDataProvider
	 * @test
	 */
	public function getSubFolderOfCurrentUrlReturnsString($leadingSlash, $trailingSlash, $host, $url, $expectedResult) {
		$result = \In2code\Powermail\Utility\Div::getSubFolderOfCurrentUrl($leadingSlash, $trailingSlash, $host, $url);
		$this->assertSame($result, $expectedResult);
	}
}