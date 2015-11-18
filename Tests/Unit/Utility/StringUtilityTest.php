<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\StringUtility;
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
 * StringUtility Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class StringUtilityTest extends UnitTestCase
{

    /**
     * Dataprovider isNotEmptyReturnsBool()
     *
     * @return array
     */
    public function isNotEmptyReturnsBoolDataProvider()
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
            ),
            'int 0' => array(
                0,
                true
            ),
            'int 1' => array(
                1,
                true
            ),
            'float 0.0' => array(
                0.0,
                true
            ),
            'float 1.0' => array(
                1.0,
                true
            ),
            'null' => array(
                null,
                false
            ),
            'bool false' => array(
                false,
                false
            ),
            'bool true' => array(
                true,
                false
            ),
            'array: string empty' => array(
                array(''),
                false
            ),
            'array: int 0' => array(
                array(0),
                true
            ),
            'array: int 1' => array(
                array(1),
                true
            ),
            'array: "abc" => "def"' => array(
                array('abc' => 'def'),
                true
            ),
            'array: empty' => array(
                array(),
                false
            ),
        );
    }

    /**
     * Test for isNotEmpty()
     *
     * @param string $value
     * @param array $expectedResult
     * @return void
     * @dataProvider isNotEmptyReturnsBoolDataProvider
     * @test
     */
    public function isNotEmptyReturnsBool($value, $expectedResult)
    {
        $this->assertSame($expectedResult, StringUtility::isNotEmpty($value));
    }

    /**
     * Data Provider for getRandomStringAlwaysReturnsStringsOfGivenLength
     *
     * @return array
     */
    public function getRandomStringAlwaysReturnsStringsOfGivenLengthDataProvider()
    {
        return array(
            'default params' => array(
                32,
                true,
            ),
            'default length lowercase' => array(
                32,
                false,
            ),
            '60 length' => array(
                60,
                true,
            ),
            '60 length lowercase' => array(
                60,
                false,
            ),
        );
    }

    /**
     * getRandomStringAlwaysReturnsStringsOfGivenLength Test
     *
     * @param int $length
     * @param bool $uppercase
     * @dataProvider getRandomStringAlwaysReturnsStringsOfGivenLengthDataProvider
     * @return void
     * @test
     */
    public function getRandomStringAlwaysReturnsStringsOfGivenLength($length, $uppercase)
    {
        for ($i = 0; $i < 100; $i++) {
            $string = StringUtility::getRandomString($length, $uppercase);

            $regex = '~[a-z0-9]{' . $length . '}~';
            if ($uppercase) {
                $regex = '~[a-zA-Z0-9]{' . $length . '}~';
            }

            $this->assertSame(1, preg_match($regex, $string));
        }
    }

    /**
     * Data Provider for conditionalVariableReturnsMixed()
     *
     * @return array
     */
    public function conditionalVariableReturnsMixedDataProvider()
    {
        return array(
            array(
                'string',
                'fallbackstring',
                'string'
            ),
            array(
                array('abc'),
                array('def'),
                array('abc')
            ),
            array(
                '',
                'fallback',
                'fallback'
            ),
            array(
                null,
                true,
                true
            ),
            array(
                123,
                234,
                123
            )
        );
    }

    /**
     * conditionalVariable Test
     *
     * @param mixed $variable
     * @param mixed $fallback
     * @param mixed $expectedResult
     * @dataProvider conditionalVariableReturnsMixedDataProvider
     * @return void
     * @test
     */
    public function conditionalVariableReturnsMixed($variable, $fallback, $expectedResult)
    {
        $this->assertSame($expectedResult, StringUtility::conditionalVariable($variable, $fallback));
    }

    /**
     * Data Provider for endsWithReturnsString()
     *
     * @return array
     */
    public function endsWithReturnsStringDataProvider()
    {
        return array(
            array(
                'xFinisher',
                'Finisher',
                true
            ),
            array(
                'inisher',
                'Finisher',
                false
            ),
            array(
                'abc',
                'c',
                true
            ),
            array(
                'abc',
                'bc',
                true
            ),
            array(
                'abc',
                'abc',
                true
            ),
        );
    }

    /**
     * endsWith Test
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $expectedResult
     * @dataProvider endsWithReturnsStringDataProvider
     * @return void
     * @test
     */
    public function endsWithReturnsString($haystack, $needle, $expectedResult)
    {
        $this->assertSame($expectedResult, StringUtility::endsWith($haystack, $needle));
    }

    /**
     * Data Provider for startsWithReturnsString()
     *
     * @return array
     */
    public function startsWithReturnsStringDataProvider()
    {
        return array(
            array(
                'Finisherx',
                'Finisher',
                true
            ),
            array(
                'inisher',
                'Finisher',
                false
            ),
            array(
                'abc',
                'a',
                true
            ),
            array(
                'abc',
                'ab',
                true
            ),
            array(
                'abc',
                'abc',
                true
            ),
        );
    }

    /**
     * startsWith Test
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $expectedResult
     * @dataProvider startsWithReturnsStringDataProvider
     * @return void
     * @test
     */
    public function startsWithReturnsString($haystack, $needle, $expectedResult)
    {
        $this->assertSame($expectedResult, StringUtility::startsWith($haystack, $needle));
    }

    /**
     * Data Provider for removeLastDotReturnsString()
     *
     * @return array
     */
    public function removeLastDotReturnsStringDataProvider()
    {
        return array(
            array(
                'abc',
                'abc'
            ),
            array(
                'abc.',
                'abc'
            ),
            array(
                '.abc.',
                '.abc'
            ),
            array(
                '.a.b.c.',
                '.a.b.c'
            ),
            array(
                'abc..',
                'abc.'
            ),
        );
    }

    /**
     * removeLastDot Test
     *
     * @param string $string
     * @param string $expectedResult
     * @dataProvider removeLastDotReturnsStringDataProvider
     * @return void
     * @test
     */
    public function removeLastDotReturnsString($string, $expectedResult)
    {
        $this->assertSame($expectedResult, StringUtility::removeLastDot($string));
    }
}
