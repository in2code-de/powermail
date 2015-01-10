<?php
namespace In2code\Powermail\Tests\ViewHelpers\Misc;

use \TYPO3\CMS\Core\Tests\UnitTestCase,
	\In2code\Powermail\Domain\Model\Field,
	\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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
 * PrefillFieldViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class PrefillFieldViewHelperTest extends UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $abstractValidationViewHelperMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->abstractValidationViewHelperMock = $this->getAccessibleMock(
			'\In2code\Powermail\ViewHelpers\Misc\PrefillFieldViewHelper',
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
	 * Dataprovider for getDefaultValueReturnsString()
	 *
	 * @return array
	 */
	public function getDefaultValueReturnsStringDataProvider() {
		return array(
			array(
				array(
					'uid' => 123,
					'marker' => 'marker',
					'prefillValue' => 'mno'
				),
				array(
					'field' => array(
						'marker' => 'abc',
						'123' => 'ghi'
					),
					'marker' => 'def',
					'uid123' => 'jkl'
				),
				array(
					'prefill.' => array(
						'marker' => 'pqr'
					)
				),
				'abc'
			),
			array(
				array(
					'uid' => 123,
					'marker' => 'marker',
					'prefillValue' => 'mno'
				),
				array(
					'field' => array(
						'123' => 'ghi'
					),
					'marker' => 'def',
					'uid123' => 'jkl'
				),
				array(
					'prefill.' => array(
						'marker' => 'pqr'
					)
				),
				'def'
			),
			array(
				array(
					'uid' => 123,
					'marker' => 'marker',
					'prefillValue' => 'mno'
				),
				array(
					'field' => array(
						'123' => 'ghi'
					),
					'uid123' => 'jkl'
				),
				array(
					'prefill.' => array(
						'marker' => 'pqr'
					)
				),
				'ghi'
			),
			array(
				array(
					'uid' => 123,
					'marker' => 'marker',
					'prefillValue' => 'mno'
				),
				array(
					'uid123' => 'jkl'
				),
				array(
					'prefill.' => array(
						'marker' => 'pqr'
					)
				),
				'jkl'
			),
			array(
				array(
					'uid' => 123,
					'marker' => 'marker',
					'prefillValue' => 'mno'
				),
				array(),
				array(
					'prefill.' => array(
						'marker' => 'pqr'
					)
				),
				'mno'
			),
			array(
				array(
					'uid' => 123,
					'marker' => 'marker',
					'prefillValue' => 'mno'
				),
				array(),
				array(),
				'mno'
			),
			array(
				array(
					'uid' => 123,
					'marker' => 'marker'
				),
				array(),
				array(
					'prefill.' => array(
						'marker' => 'pqr'
					)
				),
				'pqr'
			),
			array(
				array(
					'uid' => 123,
					'marker' => 'marker',
					'prefillValue' => 'mno'
				),
				array(
					'field' => array(
						'marker' => '',
						'123' => 'ghi'
					),
					'marker' => 'def',
					'uid123' => 'jkl'
				),
				array(
					'prefill.' => array(
						'marker' => 'pqr'
					)
				),
				''
			),
			array(
				array(
					'uid' => 123,
					'marker' => 'marker',
				),
				array(),
				array(),
				''
			),
		);
	}

	/**
	 * Test for getDefaultValue()
	 *
	 * @param array $fieldValues
	 * @param array $piVars
	 * @param array $settings
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider getDefaultValueReturnsStringDataProvider
	 * @test
	 */
	public function getDefaultValueReturnsString($fieldValues, $piVars, $settings, $expectedResult) {
		$field = new Field();
		foreach ($fieldValues as $name => $value) {
			$field->_setProperty($name, $value);
		}
		$this->abstractValidationViewHelperMock->_set('cObj', new ContentObjectRenderer());
		$this->abstractValidationViewHelperMock->_set('piVars', $piVars);
		$this->abstractValidationViewHelperMock->_set('settings', $settings);
		$this->assertSame(
			$expectedResult,
			$this->abstractValidationViewHelperMock->_callRef('getDefaultValue', $field)
		);
	}
}