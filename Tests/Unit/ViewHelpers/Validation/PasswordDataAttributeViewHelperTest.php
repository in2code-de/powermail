<?php
namespace In2code\Powermail\Tests\ViewHelpers\Validation;

use \TYPO3\CMS\Core\Tests\UnitTestCase,
	\In2code\Powermail\Domain\Model\Field,
	\TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext,
	\TYPO3\CMS\Extbase\Mvc\Request;

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
 * CaptchaDataAttributeViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class PasswordDataAttributeViewHelperTest extends UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $abstractValidationViewHelperMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->abstractValidationViewHelperMock = $this->getAccessibleMock(
			'\In2code\Powermail\ViewHelpers\Validation\PasswordValidationDataAttributeViewHelper',
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
	 * Dataprovider for render()
	 *
	 * @return array
	 */
	public function renderReturnsArrayDataProvider() {
		return array(
			'passwordWithNativevalidationAndClientvalidation' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					)
				),
				array(),
				array(),
				array(
					'index' => 0
				),
				array(
					'data-parsley-equalto' => '#powermail_field_uid',
					'data-parsley-equalto-message' => 'Both Password-Fields are not equal!'
				)
			),
			'passwordWithNativevalidation' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '0'
					)
				),
				array(),
				array(),
				array(
					'index' => 0
				),
				array()
			),
			'passwordWithClientvalidation' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '1'
					)
				),
				array(),
				array(),
				array(
					'index' => 0
				),
				array(
					'data-parsley-equalto' => '#powermail_field_uid',
					'data-parsley-equalto-message' => 'Both Password-Fields are not equal!'
				)
			),
			'passwordWithoutValidation' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '0'
					)
				),
				array(),
				array(),
				array(
					'index' => 0
				),
				array()
			),
		);
	}

	/**
	 * Test for render()
	 *
	 * @param array $settings
	 * @param array $fieldProperties
	 * @param array $additionalAttributes
	 * @param mixed $iteration
	 * @param array $expectedResult
	 * @return void
	 * @dataProvider renderReturnsArrayDataProvider
	 * @test
	 */
	public function renderReturnsArray($settings, $fieldProperties, $additionalAttributes, $iteration, $expectedResult) {
		$field = new Field;
		foreach ($fieldProperties as $propertyName => $propertyValue) {
			$field->_setProperty($propertyName, $propertyValue);
		}

		$this->abstractValidationViewHelperMock->_set('settings', $settings);
		$this->abstractValidationViewHelperMock->_set('extensionName', 'powermail');

		$controllerContext = new ControllerContext;
		$request = new Request;
		$request->setControllerExtensionName('powermail');
		$controllerContext->setRequest($request);
		$this->abstractValidationViewHelperMock->_set('controllerContext', $controllerContext);

		$result = $this->abstractValidationViewHelperMock->_callRef('render', $field, $additionalAttributes, $iteration);
		$this->assertSame($expectedResult, $result);
	}

}