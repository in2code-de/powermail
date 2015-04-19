<?php
namespace In2code\Powermail\Tests\Utility;

use TYPO3\CMS\Core\Tests\UnitTestCase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Export Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class ExportTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Utility\Export
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Utility\Export',
			array('dummy')
		);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->generalValidatorMock);
	}

	/**
	 * Data Provider for getRelativeTemplatePathAndFileNameReturnsString()
	 *
	 * @return array
	 */
	public function getRelativeTemplatePathAndFileNameReturnsStringDataProvider() {
		return array(
			array(
				'csv',
				'Templates/Module/ExportCsv.html'
			),
			array(
				'xls',
				'Templates/Module/ExportXls.html'
			),
			array(
				'bullshit',
				'Templates/Module/ExportXls.html'
			),
			array(
				NULL,
				'Templates/Module/ExportXls.html'
			),
		);
	}

	/**
	 * getRelativeTemplatePathAndFileName Test
	 *
	 * @param string $format
	 * @param string $expectedResult
	 * @dataProvider getRelativeTemplatePathAndFileNameReturnsStringDataProvider
	 * @return void
	 * @test
	 */
	public function getRelativeTemplatePathAndFileNameReturnsString($format, $expectedResult) {
		$this->generalValidatorMock->setFormat($format);
		$this->assertSame($this->generalValidatorMock->_call('getRelativeTemplatePathAndFileName'), $expectedResult);
	}

	/**
	 * Data Provider for getFormatReturnsString()
	 *
	 * @return array
	 */
	public function getFormatReturnsStringDataProvider() {
		return array(
			array(
				'csv',
				'csv'
			),
			array(
				'xls',
				'xls'
			),
			array(
				NULL,
				'xls'
			),
			array(
				'XLS',
				'xls'
			),
			array(
				'CSV',
				'xls'
			),
		);
	}

	/**
	 * getFormat Test
	 *
	 * @param string $format
	 * @param string $expectedResult
	 * @dataProvider getFormatReturnsStringDataProvider
	 * @return void
	 * @test
	 */
	public function getFormatReturnsString($format, $expectedResult) {
		$this->generalValidatorMock->setFormat($format);
		$this->assertSame($this->generalValidatorMock->_call('getFormat'), $expectedResult);
	}
}