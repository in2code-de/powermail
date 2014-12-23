<?php
namespace In2code\Powermail\Tests\ViewHelpers\Validation;

use \TYPO3\CMS\Core\Tests\UnitTestCase;

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
 * AbstractValidationViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class AbstractValidationViewHelperTest extends UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $abstractValidationViewHelperMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->abstractValidationViewHelperMock = $this->getAccessibleMock(
			'\In2code\Powermail\ViewHelpers\Validation\AbstractValidationViewHelper',
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
	 * Dataprovider for
	 * 		isNativeValidationEnabledReturnsBool()
	 * 		isClientValidationEnabledReturnsBool()
	 *
	 * @return array
	 */
	public function isValidationEnabledReturnsBoolDataProvider() {
		return array(
			'nativeAndClientActivated' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					)
				),
				TRUE,
				TRUE
			),
			'nativeOnlyActivated' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '0'
					)
				),
				TRUE,
				FALSE
			),
			'clientOnlyActivated' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '1'
					)
				),
				FALSE,
				TRUE
			),
			'nothingActivated' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '0'
					)
				),
				FALSE,
				FALSE
			),
		);
	}

	/**
	 * Test for isNativeValidationEnabled()
	 *
	 * @param array $settings
	 * @param bool $expectedNativeResult
	 * @param bool $expectedClientResult
	 * @return void
	 * @dataProvider isValidationEnabledReturnsBoolDataProvider
	 * @test
	 */
	public function isNativeValidationEnabledReturnsBool($settings, $expectedNativeResult, $expectedClientResult) {
		unset($expectedClientResult);
		$this->abstractValidationViewHelperMock->_set('settings', $settings);
		$result = $this->abstractValidationViewHelperMock->_callRef('isNativeValidationEnabled');
		$this->assertSame($expectedNativeResult, $result);
	}

	/**
	 * Test for isClientValidationEnabled()
	 *
	 * @param array $settings
	 * @param bool $expectedNativeResult
	 * @param bool $expectedClientResult
	 * @return void
	 * @dataProvider isValidationEnabledReturnsBoolDataProvider
	 * @test
	 */
	public function isClientValidationEnabledReturnsBool($settings, $expectedNativeResult, $expectedClientResult) {
		unset($expectedNativeResult);
		$this->abstractValidationViewHelperMock->_set('settings', $settings);
		$result = $this->abstractValidationViewHelperMock->_callRef('isClientValidationEnabled');
		$this->assertSame($expectedClientResult, $result);
	}
}