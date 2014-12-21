<?php
namespace In2code\Powermail\Tests\ViewHelpers\Validation;

use \TYPO3\CMS\Core\Tests\UnitTestCase,
	\In2code\Powermail\Domain\Model\Field;

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
 * FieldTypeFromValidationViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class FieldTypeFromValidationViewHelperTest extends UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $abstractValidationViewHelperMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->abstractValidationViewHelperMock = $this->getAccessibleMock(
			'\In2code\Powermail\ViewHelpers\Validation\FieldTypeFromValidationViewHelper',
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
	public function renderReturnsStringDataProvider() {
		return array(
			'defaultWithHtml5' => array(
				0,
				'text',
				TRUE
			),
			'defaultWithoutHtml5' => array(
				0,
				'text',
				FALSE
			),
			'emailValidationWithoutHtml5' => array(
				1,
				'text',
				FALSE
			),
			'emailValidationWithHtml5' => array(
				1,
				'email',
				TRUE
			),
			'urlValidationWithoutHtml5' => array(
				2,
				'text',
				FALSE
			),
			'urlValidationWithHtml5' => array(
				2,
				'url',
				TRUE
			),
			'telValidationWithoutHtml5' => array(
				3,
				'text',
				FALSE
			),
			'telValidationWithHtml5' => array(
				3,
				'tel',
				TRUE
			),
			'numberValidationWithoutHtml5' => array(
				4,
				'text',
				FALSE
			),
			'numberValidationWithHtml5' => array(
				4,
				'number',
				TRUE
			),
			'rangeValidationWithoutHtml5' => array(
				8,
				'text',
				FALSE
			),
			'rangeValidationWithHtml5' => array(
				8,
				'range',
				TRUE
			),
		);
	}

	/**
	 * Test for render()
	 *
	 * @param string $validation
	 * @param string $expectedResult
	 * @param bool $nativeValidationEnabled
	 * @return void
	 * @dataProvider renderReturnsStringDataProvider
	 * @test
	 */
	public function renderReturnsString($validation, $expectedResult, $nativeValidationEnabled) {
		$this->abstractValidationViewHelperMock->_set(
			'settings',
			array(
				'validation' => array(
					'native' => ($nativeValidationEnabled ? '1' : '0')
				)
			)
		);
		$field = new Field;
		$field->setValidation($validation);

		$result = $this->abstractValidationViewHelperMock->_callRef('render', $field);
		$this->assertSame($expectedResult, $result);
	}

}