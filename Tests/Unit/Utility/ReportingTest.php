<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\Reporting;
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
 * Reporting Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class ReportingTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Utility\Reporting
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Utility\Reporting',
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
	 * Data Provider for sortReportingArrayDescendingReturnsVoid()
	 *
	 * @return array
	 */
	public function sortReportingArrayDescendingReturnsVoidDataProvider() {
		return array(
			array(
				array(
					array(
						'blue' => 5,
						'black' => 1,
						'red' => 2,
						'yellow' => 9
					)
				),
				array(
					array(
						'yellow' => 9,
						'blue' => 5,
						'red' => 2,
						'black' => 1
					)
				)
			),
			array(
				array(
					array(
						'a' => 5,
						'' => 11,
						'23' => 2,
						'x ' => 9
					)
				),
				array(
					array(
						'' => 11,
						'x ' => 9,
						'a' => 5,
						'23' => 2
					)
				)
			),
		);
	}

	/**
	 * getRelativeTemplatePathAndFileName Test
	 *
	 * @param array $array
	 * @param array $expectedResult
	 * @dataProvider sortReportingArrayDescendingReturnsVoidDataProvider
	 * @return void
	 * @test
	 */
	public function sortReportingArrayDescendingReturnsVoid($array, $expectedResult) {
		Reporting::sortReportingArrayDescending($array);
		$this->assertSame($array, $expectedResult);
	}

	/**
	 * Data Provider for cutArrayByKeyLimitAndAddTotalValuesReturnsVoid()
	 *
	 * @return array
	 */
	public function cutArrayByKeyLimitAndAddTotalValuesReturnsVoidDataProvider() {
		return array(
			array(
				array(
					array(
						'blue' => 5,
						'black' => 1,
						'red' => 2,
						'yellow' => 9
					)
				),
				array(
					array(
						'blue' => 5,
						'black' => 1,
						'others' => 11,
					)
				)
			),
			array(
				array(
					array(
						'blue' => 2,
						'black' => 3,
						'red' => 4,
						'yellow' => 5,
						'brown' => 6,
						'pink' => 7,
						'orange' => 8,
						'violet' => 9,
						'green' => 3
					)
				),
				array(
					array(
						'blue' => 2,
						'black' => 3,
						'others' => 42,
					)
				)
			),
		);
	}

	/**
	 * getRelativeTemplatePathAndFileName Test
	 *
	 * @param array $array
	 * @param array $expectedResult
	 * @dataProvider cutArrayByKeyLimitAndAddTotalValuesReturnsVoidDataProvider
	 * @return void
	 * @test
	 */
	public function cutArrayByKeyLimitAndAddTotalValuesReturnsVoid($array, $expectedResult) {
		Reporting::cutArrayByKeyLimitAndAddTotalValues($array, 3, 'others');
		$this->assertSame($array, $expectedResult);
	}
}