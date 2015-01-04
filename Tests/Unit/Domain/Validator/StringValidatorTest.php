<?php
namespace In2code\Powermail\Tests\Domain\Validator;

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
 * StringValidator Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class StringValidatorTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Domain\Validator\StringValidator
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Domain\Validator\StringValidator',
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
	 * Dataprovider validateMandatoryForStringOrArrayReturnsBool()
	 *
	 * @return array
	 */
	public function validateMandatoryForStringOrArrayReturnsBoolDataProvider() {
		// see test \In2code\Powermail\Tests\Utility\DivTest::isNotEmptyReturnsBool()
		return array(
			'string "in2code.de"' => array(
				'in2code.de',
				TRUE
			),
			'string "a"' => array(
				'a',
				TRUE
			),
			'string empty' => array(
				'',
				FALSE
			),
			'string "0"' => array(
				'0',
				TRUE
			)
		);
	}

	/**
	 * Test for validateMandatory()
	 *
	 * @param \string $value
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validateMandatoryForStringOrArrayReturnsBoolDataProvider
	 * @test
	 */
	public function validateMandatoryForStringOrArrayReturnsBool($value, $expectedResult) {
		$result = $this->generalValidatorMock->_callRef('validateMandatory', $value);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider validateEmailReturnsBool()
	 *
	 * @return array
	 */
	public function validateEmailReturnsBoolDataProvider() {
		return array(
			'email' => array(
				'alexander.kellner@in2code.de',
				TRUE
			),
			'email2' => array(
				'www.alexander.kellner@in2code.de',
				TRUE
			),
			'email3' => array(
				'alex@subdomain1.subdomain2.in2code.de',
				TRUE
			),
			'email4' => array(
				'www.alexander.kellner@subdomain1.subdomain2.in2code.de',
				TRUE
			),
			'email5' => array(
				'alex@lalala.',
				FALSE
			),
			'email6' => array(
				'alex@lalala',
				FALSE
			),
			'email7' => array(
				'alex.lalala.de',
				FALSE
			),
			'email8' => array(
				'alex',
				FALSE
			),
		);
	}

	/**
	 * Test for validateEmail()
	 *
	 * @param \string $value
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validateEmailReturnsBoolDataProvider
	 * @test
	 */
	public function validateEmailReturnsBool($value, $expectedResult) {
		$result = $this->generalValidatorMock->_callRef('validateEmail', $value);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider validateUrlReturnsBool()
	 *
	 * @return array
	 */
	public function validateUrlReturnsBoolDataProvider() {
		return array(
			'url1' => array(
				'http://www.test.de',
				TRUE
			),
			'url2' => array(
				'www.test.de',
				FALSE
			),
			'url3' => array(
				'test.de',
				FALSE
			),
			'url4' => array(
				'https://www.test.de',
				TRUE
			),
			'url5' => array(
				'https://www.test.de',
				TRUE
			),
			'url6' => array(
				'ftp://www.test.de',
				TRUE
			),
			'url7' => array(
				'test',
				FALSE
			),
		);
	}

	/**
	 * Test for validateUrl()
	 *
	 * @param \string $value
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validateUrlReturnsBoolDataProvider
	 * @test
	 */
	public function validateUrlReturnsBool($value, $expectedResult) {
		$result = $this->generalValidatorMock->_callRef('validateUrl', $value);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider validatePhoneReturnsBool()
	 *
	 * @return array
	 */
	public function validatePhoneReturnsBoolDataProvider() {
		return array(
			'phone1' => array(
				'01234567890',
				TRUE
			),
			'phone2' => array(
				'0123 4567890',
				TRUE
			),
			'phone3' => array(
				'0123 456 789',
				TRUE
			),
			'phone4' => array(
				'(0123) 45678 - 90',
				TRUE
			),
			'phone5' => array(
				'0012 345 678 9012',
				TRUE
			),
			'phone6' => array(
				'0012 (0)345 / 67890 - 12',
				TRUE
			),
			'phone7' => array(
				'+123456789012',
				TRUE
			),
			'phone8' => array(
				'+12 345 678 9012',
				TRUE
			),
			'phone9' => array(
				'+12 3456 7890123',
				TRUE
			),
			'phone10' => array(
				'+49 (0) 123 3456789',
				TRUE
			),
			'phone11' => array(
				'+49 (0)123 / 34567 - 89',
				TRUE
			),
			'phone12' => array(
				'a123546',
				FALSE
			),
			'phone13' => array(
				'12(3)45',
				FALSE
			),
			'phone14' => array(
				'ab cd ef',
				FALSE
			),
			'phone15' => array(
				'0 123 456 7890',
				FALSE
			),
			'phone16' => array(
				'+49 (0) 36 43/58 xx xx',
				FALSE
			),
			'phone17' => array(
				'+3a',
				FALSE
			),
			'phone18' => array(
				'0',
				FALSE
			),
			'phone19' => array(
				0,
				FALSE
			),
		);
	}

	/**
	 * Test for validatePhone()
	 *
	 * @param \string $value
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validatePhoneReturnsBoolDataProvider
	 * @test
	 */
	public function validatePhoneReturnsBool($value, $expectedResult) {
		$result = $this->generalValidatorMock->_callRef('validatePhone', $value);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider validateNumbersOnlyReturnsBool()
	 *
	 * @return array
	 */
	public function validateNumbersOnlyReturnsBoolDataProvider() {
		return array(
			'number1' => array(
				'123453',
				TRUE
			),
			'number2' => array(
				'abc',
				FALSE
			),
			'number3' => array(
				'123a',
				FALSE
			),
			'number4' => array(
				'a1234',
				FALSE
			),
			'number5' => array(
				'1234 5678',
				FALSE
			),
			'number6' => array(
				123453,
				TRUE
			),
		);
	}

	/**
	 * Test for validateNumbersOnly()
	 *
	 * @param \string $value
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validateNumbersOnlyReturnsBoolDataProvider
	 * @test
	 */
	public function validateNumbersOnlyReturnsBool($value, $expectedResult) {
		$result = $this->generalValidatorMock->_callRef('validateNumbersOnly', $value);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider validateLettersOnlyReturnsBool()
	 *
	 * @return array
	 */
	public function validateLettersOnlyReturnsBoolDataProvider() {
		return array(
			'letter1' => array(
				'123453',
				FALSE
			),
			'letter2' => array(
				12345,
				FALSE
			),
			'letter3' => array(
				'abcdef',
				TRUE
			),
			'letter4' => array(
				'abc def',
				FALSE
			),
			'letter5' => array(
				'1abcdef',
				FALSE
			),
			'letter6' => array(
				'abcdef1',
				FALSE
			),
			'letter7' => array(
				'abcdefäöüßÄ',
				FALSE
			),
			'letter8' => array(
				'abd+d',
				FALSE
			),
			'letter9' => array(
				'abd.d',
				FALSE
			),
		);
	}

	/**
	 * Test for validateLettersOnly()
	 *
	 * @param \string $value
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validateLettersOnlyReturnsBoolDataProvider
	 * @test
	 */
	public function validateLettersOnlyReturnsBool($value, $expectedResult) {
		$result = $this->generalValidatorMock->_callRef('validateLettersOnly', $value);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider validateMinNumberReturnsBool()
	 *
	 * @return array
	 */
	public function validateMinNumberReturnsBoolDataProvider() {
		return array(
			'minimum1' => array(
				'8',
				'1',
				TRUE
			),
			'minimum2' => array(
				'1',
				'8',
				FALSE
			),
			'minimum3' => array(
				'4582',
				'4581',
				TRUE
			),
			'minimum4' => array(
				'0',
				'0',
				TRUE
			),
			'minimum5' => array(
				'-1',
				'1',
				FALSE
			),
			'minimum6' => array(
				'6.5',
				'6',
				TRUE
			),
			'minimum7' => array(
				5,
				4,
				TRUE
			),
			'minimum8' => array(
				4,
				5,
				FALSE
			),
			'minimum9' => array(
				5,
				5,
				TRUE
			),
		);
	}

	/**
	 * Test for validateMinNumber()
	 *
	 * @param \string $value
	 * @param \string $configuration
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validateMinNumberReturnsBoolDataProvider
	 * @test
	 */
	public function validateMinNumberReturnsBool($value, $configuration, $expectedResult) {
		$result = $this->generalValidatorMock->_callRef('validateMinNumber', $value, $configuration);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider validateMaxNumberReturnsBool()
	 *
	 * @return array
	 */
	public function validateMaxNumberReturnsBoolDataProvider() {
		return array(
			'maximum1' => array(
				'8',
				'1',
				FALSE
			),
			'maximum2' => array(
				'1',
				'8',
				TRUE
			),
			'maximum3' => array(
				'4582',
				'4581',
				FALSE
			),
			'maximum4' => array(
				'0',
				'0',
				TRUE
			),
			'maximum5' => array(
				'-1',
				'1',
				TRUE
			),
			'maximum6' => array(
				'6.5',
				'6',
				FALSE
			),
			'maximum7' => array(
				5,
				4,
				FALSE
			),
			'maximum8' => array(
				4,
				5,
				TRUE
			),
			'maximum9' => array(
				5,
				5,
				TRUE
			),
		);
	}

	/**
	 * Test for validateMaxNumber()
	 *
	 * @param \string $value
	 * @param \string $configuration
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validateMaxNumberReturnsBoolDataProvider
	 * @test
	 */
	public function validateMaxNumberReturnsBool($value, $configuration, $expectedResult) {
		$result = $this->generalValidatorMock->_callRef('validateMaxNumber', $value, $configuration);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider validateRangeReturnsBool()
	 *
	 * @return array
	 */
	public function validateRangeReturnsBoolDataProvider() {
		return array(
			'range1' => array(
				'8',
				'1,10',
				TRUE
			),
			'range2' => array(
				'507',
				'506,508',
				TRUE
			),
			'range3' => array(
				'0',
				'0,0',
				TRUE
			),
			'range4' => array(
				'5',
				'10',
				TRUE
			),
			'range5' => array(
				'15',
				'10',
				FALSE
			),
			'range6' => array(
				88,
				5,
				FALSE
			),
			'range7' => array(
				5,
				'5,6',
				TRUE
			),
			'range8' => array(
				6,
				'5,6',
				TRUE
			),
		);
	}

	/**
	 * Test for validateRange()
	 *
	 * @param \string $value
	 * @param \string $configuration
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validateRangeReturnsBoolDataProvider
	 * @test
	 */
	public function validateRangeReturnsBool($value, $configuration, $expectedResult) {
		$result = $this->generalValidatorMock->_callRef('validateRange', $value, $configuration);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider validateLengthReturnsBool()
	 *
	 * @return array
	 */
	public function validateLengthReturnsBoolDataProvider() {
		return array(
			'length1' => array(
				'abc',
				'1,10',
				TRUE
			),
			'length2' => array(
				'abcdefghijklmnopq',
				'1,10',
				FALSE
			),
			'length3' => array(
				'',
				'1,10',
				FALSE
			),
			'length4' => array(
				12345,
				'1,10',
				TRUE
			),
			'length5' => array(
				12345,
				'10',
				TRUE
			),
			'length6' => array(
				'12345',
				'10',
				TRUE
			),
			'length7' => array(
				'12345',
				'1',
				FALSE
			),
			'length8' => array(
				'12345',
				'1',
				FALSE
			),
		);
	}

	/**
	 * Test for validateLength()
	 *
	 * @param \string $value
	 * @param \string $configuration
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validateLengthReturnsBoolDataProvider
	 * @test
	 */
	public function validateLengthReturnsBool($value, $configuration, $expectedResult) {
		$result = $this->generalValidatorMock->_callRef('validateLength', $value, $configuration);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider validatePatternReturnsBool()
	 *
	 * @return array
	 */
	public function validatePatternReturnsBoolDataProvider() {
		return array(
			'pattern1' => array(
				'https://www.test.de',
				'https?://.+',
				TRUE
			),
			'pattern2' => array(
				'http://www.test.de/test/lalal.html',
				'https?://.+',
				TRUE
			),
			'pattern3' => array(
				'email@email.org',
				'https?://.+',
				FALSE
			),
			'pattern4' => array(
				'abcd',
				'https?://.+',
				FALSE
			),
			'pattern5' => array(
				1345,
				'https?://.+',
				FALSE
			),
			'pattern6' => array(
				12345,
				'[0-9]{5}',
				TRUE
			),
			'pattern7' => array(
				1234,
				'[0-9]{5}',
				FALSE
			),
		);
	}

	/**
	 * Test for validatePattern()
	 *
	 * @param \string $value
	 * @param \string $configuration
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validatePatternReturnsBoolDataProvider
	 * @test
	 */
	public function validatePatternReturnsBool($value, $configuration, $expectedResult) {
		$result = $this->generalValidatorMock->_callRef('validatePattern', $value, $configuration);
		$this->assertSame($expectedResult, $result);
	}
}