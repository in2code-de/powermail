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
 * ValidationDataAttributeViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class ValidationDataAttributeViewHelperTest extends UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $abstractValidationViewHelperMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->abstractValidationViewHelperMock = $this->getAccessibleMock(
			'\In2code\Powermail\ViewHelpers\Validation\ValidationDataAttributeViewHelper',
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
			'textWithNativevalidationAndClientvalidation' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					)
				),
				array(
					'type' => 'input',
					'mandatory' => '1'
				),
				array(),
				array(
					'index' => 0,
					'total' => 1
				),
				array(
					'required' => 'required',
					'data-parsley-required-message' => 'This field must be filled!',
					'data-parsley-trigger' => 'change'
				)
			),
			'textWithClientvalidation' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '1'
					)
				),
				array(
					'type' => 'input',
					'mandatory' => '1'
				),
				array(),
				array(
					'index' => 0,
					'total' => 1
				),
				array(
					'data-parsley-required' => 'true',
					'data-parsley-required-message' => 'This field must be filled!',
					'data-parsley-trigger' => 'change'
				)
			),
			'radioWithNativevalidationAndClientvalidation' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					)
				),
				array(
					'type' => 'radio',
					'mandatory' => '1'
				),
				array(),
				array(
					'index' => 0,
					'total' => 1
				),
				array(
					'required' => 'required',
					'data-parsley-required-message' => 'One of these fields must be filled!',
					'data-parsley-errors-container' => '.powermail_field_error_container_uid',
					'data-parsley-class-handler' => '.powermail_fieldwrap_ div:first'
				)
			),
			'radioWithClientvalidation' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '1'
					)
				),
				array(
					'type' => 'radio',
					'mandatory' => '1'
				),
				array(),
				array(
					'index' => 0,
					'total' => 1
				),
				array(
					'data-parsley-required' => 'true',
					'data-parsley-required-message' => 'One of these fields must be filled!',
					'data-parsley-errors-container' => '.powermail_field_error_container_uid',
					'data-parsley-class-handler' => '.powermail_fieldwrap_ div:first'
				)
			),
			'checkWithNativevalidationAndClientvalidation' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					)
				),
				array(
					'type' => 'check',
					'mandatory' => '1'
				),
				array(),
				array(
					'index' => 0,
					'total' => 1
				),
				array(
					'required' => 'required',
					'data-parsley-required-message' => 'One of these fields must be filled!',
					'data-parsley-errors-container' => '.powermail_field_error_container_uid',
					'data-parsley-class-handler' => '.powermail_fieldwrap_ div:first'
				)
			),
			'checkWithNativevalidationAndClientvalidationCheck2' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					)
				),
				array(
					'type' => 'check',
					'mandatory' => '1',
					'marker' => 'checkbox'
				),
				array(),
				array(
					'index' => 1,
					'total' => 2
				),
				array(
					'data-parsley-multiple' => 'checkbox'
				)
			),
			'checkWithClientvalidationCheck2' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '1'
					)
				),
				array(
					'type' => 'check',
					'mandatory' => '1',
					'marker' => 'checkbox'
				),
				array(),
				array(
					'index' => 1,
					'total' => 2
				),
				array(
					'data-parsley-multiple' => 'checkbox'
				)
			),
			'textWithNativevalidationAndClientvalidationEmailAndAdditionalAttributes' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					)
				),
				array(
					'type' => 'input',
					'marker' => 'text',
					'validation' => 1
				),
				array(
					'data-test' => 'in2code.de'
				),
				array(
					'index' => 0
				),
				array(
					'data-test' => 'in2code.de',
					'data-parsley-error-message' => 'This is not a valid email address!'
				)
			),
			'textWithClientvalidationEmailAndAdditionalAttributes' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '1'
					)
				),
				array(
					'type' => 'input',
					'marker' => 'text',
					'validation' => 1
				),
				array(
					'data-email' => 'service@in2code.de'
				),
				array(
					'index' => 0
				),
				array(
					'data-email' => 'service@in2code.de',
					'data-parsley-type' => 'email',
					'data-parsley-error-message' => 'This is not a valid email address!'
				)
			),
			'textWithNativevalidationAndClientvalidationRange' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					)
				),
				array(
					'type' => 'input',
					'marker' => 'text',
					'validation' => 8,
					'validationConfiguration' => '1,10'
				),
				array(),
				array(
					'index' => 0
				),
				array(
					'min' => 1,
					'max' => 10,
					'data-parsley-error-message' => 'Number to high or to low!'
				)
			),
			'textWithClientvalidationRange' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '1'
					)
				),
				array(
					'type' => 'input',
					'marker' => 'text',
					'validation' => 8,
					'validationConfiguration' => '1,10'
				),
				array(),
				array(
					'index' => 0
				),
				array(
					'data-parsley-min' => 1,
					'data-parsley-max' => 10,
					'data-parsley-error-message' => 'Number to high or to low!'
				)
			),
			'textWithClientvalidationRangeNoMin' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '1'
					)
				),
				array(
					'type' => 'input',
					'marker' => 'text',
					'validation' => 8,
					'validationConfiguration' => '99'
				),
				array(),
				array(
					'index' => 0
				),
				array(
					'data-parsley-min' => 1,
					'data-parsley-max' => 99,
					'data-parsley-error-message' => 'Number to high or to low!'
				)
			),
			'textWithNativevalidationAndClientvalidationPattern' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					)
				),
				array(
					'type' => 'input',
					'marker' => 'text',
					'validation' => 10,
					'validationConfiguration' => 'abcdefg'
				),
				array(),
				array(
					'index' => 0
				),
				array(
					'pattern' => 'abcdefg',
					'data-parsley-error-message' => 'Error in validation!'
				)
			),
			'textWithClientvalidationPattern' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '1'
					)
				),
				array(
					'type' => 'input',
					'marker' => 'text',
					'validation' => 10,
					'validationConfiguration' => 'abcdefg'
				),
				array(),
				array(
					'index' => 0
				),
				array(
					'data-parsley-pattern' => 'abcdefg',
					'data-parsley-error-message' => 'Error in validation!'
				)
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