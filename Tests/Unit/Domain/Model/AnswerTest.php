<?php
namespace In2code\Powermail\Tests\Domain\Model;

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
 * Answer Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class AnswerTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Domain\Model\Answer
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Domain\Model\Answer',
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
	 * Dataprovider getValueReturnVoid()
	 *
	 * @return array
	 */
	public function getValueReturnVoidDataProvider() {
		return array(
			'string 1' => array(
				'abc def',
				'abc def',
				0,
				NULL
			),
			'string 2' => array(
				'<\'"test"',
				'<\'"test"',
				0,
				NULL
			),
			'array 1' => array(
				json_encode(array('a')),
				array('a'),
				1,
				NULL
			),
			'array 2' => array(
				json_encode(array(1, 2, 3)),
				array(1, 2, 3),
				3,
				NULL
			),
			'date 1' => array(
				strtotime('2010-01-31'),
				'2010-01-31',
				2,
				'date'
			),
			'date 2' => array(
				strtotime('1975-10-13'),
				'1975-10-13',
				2,
				'date'
			),
			'datetime 1' => array(
				strtotime('1975-10-13 14:00'),
				'1975-10-13 14:00',
				2,
				'datetime'
			),
			'datetime 2' => array(
				strtotime('2020-01-30 22:23'),
				'2020-01-30 22:23',
				2,
				'datetime'
			),
			'time 1' => array(
				strtotime('14:00'),
				'14:00',
				2,
				'time'
			),
			'time 2' => array(
				strtotime('22:23'),
				'22:23',
				2,
				'time'
			),
		);
	}

	/**
	 * Test for getValue()
	 *
	 * @param mixed $value
	 * @param mixed $expectedResult
	 * @param int $valueType
	 * @param string $datepickerSettings
	 * @return void
	 * @dataProvider getValueReturnVoidDataProvider
	 * @test
	 */
	public function getValueReturnMixed($value, $expectedResult, $valueType = 0, $datepickerSettings = NULL) {
		if ($datepickerSettings) {
			$field = new Field;
			if ($datepickerSettings) {
				$field->setDatepickerSettings($datepickerSettings);
			}
			$this->generalValidatorMock->_callRef('setField', $field);
		}
		if ($valueType > 0) {
			$this->generalValidatorMock->_callRef('setValueType', $valueType);
		}

		$this->generalValidatorMock->_setProperty('value', $value);
		$this->assertSame($expectedResult, $this->generalValidatorMock->_callRef('getValue', $value));
	}

	/**
	 * Test for getRawValue()
	 *
	 * @param mixed $value
	 * @return void
	 * @dataProvider getValueReturnVoidDataProvider
	 * @test
	 */
	public function getRawValueReturnString($value) {
		$this->generalValidatorMock->_setProperty('value', $value);
		$this->assertSame(
			$value,
			$this->generalValidatorMock->_callRef('getRawValue')
		);
	}

	/**
	 * Dataprovider setValueReturnVoid()
	 *
	 * @return array
	 */
	public function setValueReturnVoidDataProvider() {
		return array(
			'string 1' => array(
				'abc def',
				'abc def',
				'input',
				NULL
			),
			'string 2' => array(
				'<\'"test"',
				'<\'"test"',
				'input',
				NULL
			),
			'array 1' => array(
				array('a'),
				json_encode(array('a')),
				'check',
				NULL
			),
			'array 2' => array(
				array(1, 2 ,3),
				json_encode(array(1, 2, 3)),
				'check',
				NULL
			),
			'date 1' => array(
				'2010-01-31',
				strtotime('2010-01-31'),
				'date',
				'date'
			),
			'date 2' => array(
				'1975-10-13',
				strtotime('1975-10-13'),
				'date',
				'date'
			),
			'datetime 1' => array(
				'1975-10-13 14:00',
				strtotime('1975-10-13 14:00'),
				'date',
				'datetime'
			),
			'datetime 2' => array(
				'2020-01-30 22:23',
				strtotime('2020-01-30 22:23'),
				'date',
				'datetime'
			),
			'time 1' => array(
				'14:00',
				strtotime('14:00'),
				'date',
				'time'
			),
			'time 2' => array(
				'22:23',
				strtotime('22:23'),
				'date',
				'time'
			),
		);
	}

	/**
	 * Test for setValue()
	 *
	 * @param mixed $value
	 * @param mixed $expectedResult
	 * @param string $fieldType
	 * @param string $datepickerSettings
	 * @return void
	 * @dataProvider setValueReturnVoidDataProvider
	 * @test
	 */
	public function setValueReturnVoid($value, $expectedResult, $fieldType = NULL, $datepickerSettings = NULL) {
		if ($fieldType || $datepickerSettings) {
			$field = new Field;
			if ($fieldType) {
				$field->setType($fieldType);
			}
			if ($datepickerSettings) {
				$field->setDatepickerSettings($datepickerSettings);
			}
			$this->generalValidatorMock->_callRef('setField', $field);
		}

		$this->generalValidatorMock->_callRef('setValue', $value);
		$this->assertSame($expectedResult, $this->generalValidatorMock->_getProperty('value'));
	}
}