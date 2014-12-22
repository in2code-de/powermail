<?php
namespace In2code\Powermail\Tests\ViewHelpers\Reporting;

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
 * GetLabelsGoogleChartsViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class GetLabelsGoogleChartsViewHelperTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $abstractValidationViewHelperMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->abstractValidationViewHelperMock = $this->getAccessibleMock(
			'\In2code\Powermail\ViewHelpers\Reporting\GetLabelsGoogleChartsViewHelper',
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
	 * Dataprovider for renderReturnsString()
	 *
	 * @return array
	 */
	public function renderReturnsStringDataProvider() {
		return array(
			array(
				array(
					'test' => array(
						'label1' => '10',
						'label2' => '70',
						'label3' => '20',
					)
				),
				'test',
				',',
				15,
				'...',
				FALSE,
				'label1,label2,label3'
			),
			array(
				array(
					'a' => array(
						'abc' => '10',
						'def' => '70',
						'gh' => '20',
					)
				),
				'a',
				',',
				2,
				'...',
				TRUE,
				'ab...%2Cde...%2Cgh'
			),
			array(
				array(
					'0' => array(
						'typo3' => '15',
						'wordpress' => '60',
						'drupal' => '25',
					)
				),
				'0',
				'|',
				5,
				' etc...',
				FALSE,
				'typo3|wordp etc...|drupa etc...'
			),
			array(
				array(
					'0' => array(
						'"Fußgänger"' => '15',
						'"Auto, LKW, Krafträder"' => '60',
						'Fahrradfahrer' => '25',
					)
				),
				'0',
				',',
				15,
				'...',
				FALSE,
				'Fußgänger,Auto LKW Kraftr...,Fahrradfahrer'
			),
		);
	}

	/**
	 * Test for render()
	 *
	 * @param array $answers Array with answeres
	 * @param string $field Fieldname (key of answers array)
	 * @param string $separator
	 * @param int $crop
	 * @param string $append
	 * @param bool $urlEncode
	 * @return void
	 * @dataProvider renderReturnsStringDataProvider
	 * @test
	 */
	public function renderReturnsString($answers, $field, $separator, $crop, $append, $urlEncode, $expectedResult) {
		$result = $this->abstractValidationViewHelperMock->_callRef('render', $answers, $field, $separator, $crop, $append, $urlEncode);
		$this->assertSame($expectedResult, $result);
	}
}