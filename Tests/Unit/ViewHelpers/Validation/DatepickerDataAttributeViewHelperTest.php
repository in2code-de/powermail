<?php
namespace In2code\Powermail\Tests\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Request;

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
class DatepickerDataAttributeViewHelperTest extends UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $abstractValidationViewHelperMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->abstractValidationViewHelperMock = $this->getAccessibleMock(
			'\In2code\Powermail\ViewHelpers\Validation\DatepickerDataAttributeViewHelper',
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
			'datepickerWithNativevalidationAndClientvalidationAndAdditionalAttributesAndMandatory' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					)
				),
				array(
					'mandatory' => 1
				),
				array(
					'data-company' => 'in2code'
				),
				'anyvalue',
				array(
					'data-company' => 'in2code',
					'data-datepicker-force' => NULL,
					'data-datepicker-settings' => 'date',
					'data-datepicker-months' => 'datepicker_month_jan,datepicker_month_feb,datepicker_month_mar,datepicker_month_apr,datepicker_month_may,datepicker_month_jun,datepicker_month_jul,datepicker_month_aug,datepicker_month_sep,datepicker_month_oct,datepicker_month_nov,datepicker_month_dec',
					'data-datepicker-days' => 'datepicker_day_so,datepicker_day_mo,datepicker_day_tu,datepicker_day_we,datepicker_day_th,datepicker_day_fr,datepicker_day_sa',
					'data-datepicker-format' => 'Y-m-d H:i',
					'data-date-value' => 'anyvalue',
					'required' => 'required',
					'data-parsley-required-message' => 'validationerror_mandatory',
					'data-parsley-trigger' => 'change'
				)
			),
			'datepickerWithNativevalidationAndClientvalidation' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					)
				),
				array(),
				array(),
				'anyvalue',
				array(
					'data-datepicker-force' => NULL,
					'data-datepicker-settings' => 'date',
					'data-datepicker-months' => 'datepicker_month_jan,datepicker_month_feb,datepicker_month_mar,datepicker_month_apr,datepicker_month_may,datepicker_month_jun,datepicker_month_jul,datepicker_month_aug,datepicker_month_sep,datepicker_month_oct,datepicker_month_nov,datepicker_month_dec',
					'data-datepicker-days' => 'datepicker_day_so,datepicker_day_mo,datepicker_day_tu,datepicker_day_we,datepicker_day_th,datepicker_day_fr,datepicker_day_sa',
					'data-datepicker-format' => 'Y-m-d H:i',
					'data-date-value' => 'anyvalue',
				)
			),
			'datepickerWithNativevalidation' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '0'
					)
				),
				array(),
				array(),
				'',
				array(
					'data-datepicker-force' => NULL,
					'data-datepicker-settings' => 'date',
					'data-datepicker-months' => 'datepicker_month_jan,datepicker_month_feb,datepicker_month_mar,datepicker_month_apr,datepicker_month_may,datepicker_month_jun,datepicker_month_jul,datepicker_month_aug,datepicker_month_sep,datepicker_month_oct,datepicker_month_nov,datepicker_month_dec',
					'data-datepicker-days' => 'datepicker_day_so,datepicker_day_mo,datepicker_day_tu,datepicker_day_we,datepicker_day_th,datepicker_day_fr,datepicker_day_sa',
					'data-datepicker-format' => 'Y-m-d H:i',
				)
			),
			'datepickerWithClientvalidation' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '1'
					)
				),
				array(),
				array(),
				'',
				array(
					'data-datepicker-force' => NULL,
					'data-datepicker-settings' => 'date',
					'data-datepicker-months' => 'datepicker_month_jan,datepicker_month_feb,datepicker_month_mar,datepicker_month_apr,datepicker_month_may,datepicker_month_jun,datepicker_month_jul,datepicker_month_aug,datepicker_month_sep,datepicker_month_oct,datepicker_month_nov,datepicker_month_dec',
					'data-datepicker-days' => 'datepicker_day_so,datepicker_day_mo,datepicker_day_tu,datepicker_day_we,datepicker_day_th,datepicker_day_fr,datepicker_day_sa',
					'data-datepicker-format' => 'Y-m-d H:i',
				)
			),
			'datepickerWithoutValidation' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '0'
					)
				),
				array(),
				array(),
				'',
				array(
					'data-datepicker-force' => NULL,
					'data-datepicker-settings' => 'date',
					'data-datepicker-months' => 'datepicker_month_jan,datepicker_month_feb,datepicker_month_mar,datepicker_month_apr,datepicker_month_may,datepicker_month_jun,datepicker_month_jul,datepicker_month_aug,datepicker_month_sep,datepicker_month_oct,datepicker_month_nov,datepicker_month_dec',
					'data-datepicker-days' => 'datepicker_day_so,datepicker_day_mo,datepicker_day_tu,datepicker_day_we,datepicker_day_th,datepicker_day_fr,datepicker_day_sa',
					'data-datepicker-format' => 'Y-m-d H:i',
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
	 * @param string $value
	 * @param array $expectedResult
	 * @return void
	 * @dataProvider renderReturnsArrayDataProvider
	 * @test
	 */
	public function renderReturnsArray($settings, $fieldProperties, $additionalAttributes, $value, $expectedResult) {
		$field = new Field;
		foreach ($fieldProperties as $propertyName => $propertyValue) {
			$field->_setProperty($propertyName, $propertyValue);
		}
		$this->abstractValidationViewHelperMock->_set('settings', $settings);
		$this->abstractValidationViewHelperMock->_set('extensionName', 'powermail');
		$this->abstractValidationViewHelperMock->_set('test', TRUE);

		$controllerContext = new ControllerContext;
		$request = new Request;
		$request->setControllerExtensionName('powermail');
		$controllerContext->setRequest($request);
		$this->abstractValidationViewHelperMock->_set('controllerContext', $controllerContext);

		$result = $this->abstractValidationViewHelperMock->_callRef('render', $field, $additionalAttributes, $value);
		$this->assertSame($expectedResult, $result);
	}
}