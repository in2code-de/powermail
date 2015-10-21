<?php
namespace In2code\Powermail\Tests\Domain\Validator;

use TYPO3\CMS\Core\Tests\UnitTestCase;

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
 *          GNU Lesser General Public License, version 3 or later
 */
class StringValidatorTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Validator\StringValidator
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock('\In2code\Powermail\Domain\Validator\StringValidator',
            array('dummy'));
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->generalValidatorMock);
    }

    /**
     * Dataprovider validateMandatoryForStringOrArrayReturnsBool()
     *
     * @return array
     */
    public function validateMandatoryForStringOrArrayReturnsBoolDataProvider()
    {
        return array(
            'string "in2code.de"' => array(
                'in2code.de',
                true
            ),
            'string "a"' => array(
                'a',
                true
            ),
            'string empty' => array(
                '',
                false
            ),
            'string "0"' => array(
                '0',
                true
            )
        );
    }

    /**
     * Test for validateMandatory()
     *
     * @param string $value
     * @param bool $expectedResult
     * @return void
     * @dataProvider validateMandatoryForStringOrArrayReturnsBoolDataProvider
     * @test
     */
    public function validateMandatoryForStringOrArrayReturnsBool($value, $expectedResult)
    {
        $result = $this->generalValidatorMock->_callRef('validateMandatory', $value);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider validateEmailReturnsBool()
     *
     * @return array
     */
    public function validateEmailReturnsBoolDataProvider()
    {
        return array(
            'email' => array(
                'alexander.kellner@in2code.de',
                true
            ),
            'email2' => array(
                'www.alexander.kellner@in2code.de',
                true
            ),
            'email3' => array(
                'alex@subdomain1.subdomain2.in2code.de',
                true
            ),
            'email4' => array(
                'www.alexander.kellner@subdomain1.subdomain2.in2code.de',
                true
            ),
            'email5' => array(
                'alex@lalala.',
                false
            ),
            'email6' => array(
                'alex@lalala',
                false
            ),
            'email7' => array(
                'alex.lalala.de',
                false
            ),
            'email8' => array(
                'alex',
                false
            ),
        );
    }

    /**
     * Test for validateEmail()
     *
     * @param string $value
     * @param bool $expectedResult
     * @return void
     * @dataProvider validateEmailReturnsBoolDataProvider
     * @test
     */
    public function validateEmailReturnsBool($value, $expectedResult)
    {
        $result = $this->generalValidatorMock->_callRef('validateEmail', $value);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider validateUrlReturnsBool()
     *
     * @return array
     */
    public function validateUrlReturnsBoolDataProvider()
    {
        return array(
            'url1' => array(
                'http://www.test.de',
                true
            ),
            'url2' => array(
                'www.test.de',
                false
            ),
            'url3' => array(
                'test.de',
                false
            ),
            'url4' => array(
                'https://www.test.de',
                true
            ),
            'url5' => array(
                'https://www.test.de',
                true
            ),
            'url6' => array(
                'ftp://www.test.de',
                true
            ),
            'url7' => array(
                'test',
                false
            ),
        );
    }

    /**
     * Test for validateUrl()
     *
     * @param string $value
     * @param bool $expectedResult
     * @return void
     * @dataProvider validateUrlReturnsBoolDataProvider
     * @test
     */
    public function validateUrlReturnsBool($value, $expectedResult)
    {
        $result = $this->generalValidatorMock->_callRef('validateUrl', $value);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider validatePhoneReturnsBool()
     *
     * @return array
     */
    public function validatePhoneReturnsBoolDataProvider()
    {
        return array(
            'phone1' => array(
                '01234567890',
                true
            ),
            'phone2' => array(
                '0123 4567890',
                true
            ),
            'phone3' => array(
                '0123 456 789',
                true
            ),
            'phone4' => array(
                '(0123) 45678 - 90',
                true
            ),
            'phone5' => array(
                '0012 345 678 9012',
                true
            ),
            'phone6' => array(
                '0012 (0)345 / 67890 - 12',
                true
            ),
            'phone7' => array(
                '+123456789012',
                true
            ),
            'phone8' => array(
                '+12 345 678 9012',
                true
            ),
            'phone9' => array(
                '+12 3456 7890123',
                true
            ),
            'phone10' => array(
                '+49 (0) 123 3456789',
                true
            ),
            'phone11' => array(
                '+49 (0)123 / 34567 - 89',
                true
            ),
            'phone12' => array(
                'a123546',
                false
            ),
            'phone13' => array(
                '12(3)45',
                false
            ),
            'phone14' => array(
                'ab cd ef',
                false
            ),
            'phone15' => array(
                '0 123 456 7890',
                false
            ),
            'phone16' => array(
                '+49 (0) 36 43/58 xx xx',
                false
            ),
            'phone17' => array(
                '+3a',
                false
            ),
            'phone18' => array(
                '0',
                false
            ),
            'phone19' => array(
                0,
                false
            ),
        );
    }

    /**
     * Test for validatePhone()
     *
     * @param string $value
     * @param bool $expectedResult
     * @return void
     * @dataProvider validatePhoneReturnsBoolDataProvider
     * @test
     */
    public function validatePhoneReturnsBool($value, $expectedResult)
    {
        $result = $this->generalValidatorMock->_callRef('validatePhone', $value);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider validateNumbersOnlyReturnsBool()
     *
     * @return array
     */
    public function validateNumbersOnlyReturnsBoolDataProvider()
    {
        return array(
            'number1' => array(
                '123453',
                true
            ),
            'number2' => array(
                'abc',
                false
            ),
            'number3' => array(
                '123a',
                false
            ),
            'number4' => array(
                'a1234',
                false
            ),
            'number5' => array(
                '1234 5678',
                false
            ),
            'number6' => array(
                123453,
                true
            ),
        );
    }

    /**
     * Test for validateNumbersOnly()
     *
     * @param string $value
     * @param bool $expectedResult
     * @return void
     * @dataProvider validateNumbersOnlyReturnsBoolDataProvider
     * @test
     */
    public function validateNumbersOnlyReturnsBool($value, $expectedResult)
    {
        $result = $this->generalValidatorMock->_callRef('validateNumbersOnly', $value);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider validateLettersOnlyReturnsBool()
     *
     * @return array
     */
    public function validateLettersOnlyReturnsBoolDataProvider()
    {
        return array(
            'letter1' => array(
                '123453',
                false
            ),
            'letter2' => array(
                12345,
                false
            ),
            'letter3' => array(
                'abcdef',
                true
            ),
            'letter4' => array(
                'abc def',
                false
            ),
            'letter5' => array(
                '1abcdef',
                false
            ),
            'letter6' => array(
                'abcdef1',
                false
            ),
            'letter7' => array(
                'abcdefäöüßÄ',
                false
            ),
            'letter8' => array(
                'abd+d',
                false
            ),
            'letter9' => array(
                'abd.d',
                false
            ),
        );
    }

    /**
     * Test for validateLettersOnly()
     *
     * @param string $value
     * @param bool $expectedResult
     * @return void
     * @dataProvider validateLettersOnlyReturnsBoolDataProvider
     * @test
     */
    public function validateLettersOnlyReturnsBool($value, $expectedResult)
    {
        $result = $this->generalValidatorMock->_callRef('validateLettersOnly', $value);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider validateMinNumberReturnsBool()
     *
     * @return array
     */
    public function validateMinNumberReturnsBoolDataProvider()
    {
        return array(
            'minimum1' => array(
                '8',
                '1',
                true
            ),
            'minimum2' => array(
                '1',
                '8',
                false
            ),
            'minimum3' => array(
                '4582',
                '4581',
                true
            ),
            'minimum4' => array(
                '0',
                '0',
                true
            ),
            'minimum5' => array(
                '-1',
                '1',
                false
            ),
            'minimum6' => array(
                '6.5',
                '6',
                true
            ),
            'minimum7' => array(
                5,
                4,
                true
            ),
            'minimum8' => array(
                4,
                5,
                false
            ),
            'minimum9' => array(
                5,
                5,
                true
            ),
        );
    }

    /**
     * Test for validateMinNumber()
     *
     * @param string $value
     * @param string $configuration
     * @param bool $expectedResult
     * @return void
     * @dataProvider validateMinNumberReturnsBoolDataProvider
     * @test
     */
    public function validateMinNumberReturnsBool($value, $configuration, $expectedResult)
    {
        $result = $this->generalValidatorMock->_callRef('validateMinNumber', $value, $configuration);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider validateMaxNumberReturnsBool()
     *
     * @return array
     */
    public function validateMaxNumberReturnsBoolDataProvider()
    {
        return array(
            'maximum1' => array(
                '8',
                '1',
                false
            ),
            'maximum2' => array(
                '1',
                '8',
                true
            ),
            'maximum3' => array(
                '4582',
                '4581',
                false
            ),
            'maximum4' => array(
                '0',
                '0',
                true
            ),
            'maximum5' => array(
                '-1',
                '1',
                true
            ),
            'maximum6' => array(
                '6.5',
                '6',
                false
            ),
            'maximum7' => array(
                5,
                4,
                false
            ),
            'maximum8' => array(
                4,
                5,
                true
            ),
            'maximum9' => array(
                5,
                5,
                true
            ),
        );
    }

    /**
     * Test for validateMaxNumber()
     *
     * @param string $value
     * @param string $configuration
     * @param bool $expectedResult
     * @return void
     * @dataProvider validateMaxNumberReturnsBoolDataProvider
     * @test
     */
    public function validateMaxNumberReturnsBool($value, $configuration, $expectedResult)
    {
        $result = $this->generalValidatorMock->_callRef('validateMaxNumber', $value, $configuration);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider validateRangeReturnsBool()
     *
     * @return array
     */
    public function validateRangeReturnsBoolDataProvider()
    {
        return array(
            'range1' => array(
                '8',
                '1,10',
                true
            ),
            'range2' => array(
                '507',
                '506,508',
                true
            ),
            'range3' => array(
                '0',
                '0,0',
                true
            ),
            'range4' => array(
                '5',
                '10',
                true
            ),
            'range5' => array(
                '15',
                '10',
                false
            ),
            'range6' => array(
                88,
                5,
                false
            ),
            'range7' => array(
                5,
                '5,6',
                true
            ),
            'range8' => array(
                6,
                '5,6',
                true
            ),
        );
    }

    /**
     * Test for validateRange()
     *
     * @param string $value
     * @param string $configuration
     * @param bool $expectedResult
     * @return void
     * @dataProvider validateRangeReturnsBoolDataProvider
     * @test
     */
    public function validateRangeReturnsBool($value, $configuration, $expectedResult)
    {
        $result = $this->generalValidatorMock->_callRef('validateRange', $value, $configuration);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider validateLengthReturnsBool()
     *
     * @return array
     */
    public function validateLengthReturnsBoolDataProvider()
    {
        return array(
            'length1' => array(
                'abc',
                '1,10',
                true
            ),
            'length2' => array(
                'abcdefghijklmnopq',
                '1,10',
                false
            ),
            'length3' => array(
                '',
                '1,10',
                false
            ),
            'length4' => array(
                12345,
                '1,10',
                true
            ),
            'length5' => array(
                12345,
                '10',
                true
            ),
            'length6' => array(
                '12345',
                '10',
                true
            ),
            'length7' => array(
                '12345',
                '1',
                false
            ),
            'length8' => array(
                '12345',
                '1',
                false
            ),
        );
    }

    /**
     * Test for validateLength()
     *
     * @param string $value
     * @param string $configuration
     * @param bool $expectedResult
     * @return void
     * @dataProvider validateLengthReturnsBoolDataProvider
     * @test
     */
    public function validateLengthReturnsBool($value, $configuration, $expectedResult)
    {
        $result = $this->generalValidatorMock->_callRef('validateLength', $value, $configuration);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider validatePatternReturnsBool()
     *
     * @return array
     */
    public function validatePatternReturnsBoolDataProvider()
    {
        return array(
            'pattern1' => array(
                'https://www.test.de',
                'https?://.+',
                true
            ),
            'pattern2' => array(
                'http://www.test.de/test/lalal.html',
                'https?://.+',
                true
            ),
            'pattern3' => array(
                'email@email.org',
                'https?://.+',
                false
            ),
            'pattern4' => array(
                'abcd',
                'https?://.+',
                false
            ),
            'pattern5' => array(
                1345,
                'https?://.+',
                false
            ),
            'pattern6' => array(
                12345,
                '[0-9]{5}',
                true
            ),
            'pattern7' => array(
                1234,
                '[0-9]{5}',
                false
            ),
        );
    }

    /**
     * Test for validatePattern()
     *
     * @param string $value
     * @param string $configuration
     * @param bool $expectedResult
     * @return void
     * @dataProvider validatePatternReturnsBoolDataProvider
     * @test
     */
    public function validatePatternReturnsBool($value, $configuration, $expectedResult)
    {
        $result = $this->generalValidatorMock->_callRef('validatePattern', $value, $configuration);
        $this->assertSame($expectedResult, $result);
    }
}
