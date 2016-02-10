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
        return [
            'string "in2code.de"' => [
                'in2code.de',
                true
            ],
            'string "a"' => [
                'a',
                true
            ],
            'string empty' => [
                '',
                false
            ],
            'string "0"' => [
                '0',
                true
            ],
            'int 0' => [
                0,
                true
            ],
            'int 1' => [
                1,
                true
            ],
            'float 0.0' => [
                0.0,
                true
            ],
            'float 1.0' => [
                1.0,
                true
            ],
            'null' => [
                null,
                false
            ],
            'bool false' => [
                false,
                false
            ],
            'bool true' => [
                true,
                false
            ],
            'array: string empty' => [
                [''],
                false
            ],
            'array: int 0' => [
                [0],
                true
            ],
            'array: int 1' => [
                [1],
                true
            ],
            'array: "abc" => "def"' => [
                ['abc' => 'def'],
                true
            ],
            'array: empty' => [
                [],
                false
            ],
        ];
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
        return [
            'default params' => [
                32,
                true,
            ],
            'default length lowercase' => [
                32,
                false,
            ],
            '60 length' => [
                60,
                true,
            ],
            '60 length lowercase' => [
                60,
                false,
            ],
        ];
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
        return [
            [
                'string',
                'fallbackstring',
                'string'
            ],
            [
                ['abc'],
                ['def'],
                ['abc']
            ],
            [
                '',
                'fallback',
                'fallback'
            ],
            [
                null,
                true,
                true
            ],
            [
                123,
                234,
                123
            ]
        ];
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
        return [
            [
                'xFinisher',
                'Finisher',
                true
            ],
            [
                'inisher',
                'Finisher',
                false
            ],
            [
                'abc',
                'c',
                true
            ],
            [
                'abc',
                'bc',
                true
            ],
            [
                'abc',
                'abc',
                true
            ],
        ];
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
        return [
            [
                'Finisherx',
                'Finisher',
                true
            ],
            [
                'inisher',
                'Finisher',
                false
            ],
            [
                'abc',
                'a',
                true
            ],
            [
                'abc',
                'ab',
                true
            ],
            [
                'abc',
                'abc',
                true
            ],
        ];
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
        return [
            [
                'abc',
                'abc'
            ],
            [
                'abc.',
                'abc'
            ],
            [
                '.abc.',
                '.abc'
            ],
            [
                '.a.b.c.',
                '.a.b.c'
            ],
            [
                'abc..',
                'abc.'
            ],
        ];
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

    /**
     * Data Provider for br2nlReturnString()
     *
     * @return array
     */
    public function br2nlReturnStringDataProvider()
    {
        return [
            [
                'a<br>b',
                "a\nb"
            ],
            [
                'a<br><br /><br/>b',
                "a\n\n\nb"
            ],
            [
                'a\nbr[br]b',
                'a\nbr[br]b'
            ],
        ];
    }

    /**
     * cleanFileNameReturnBool Test
     *
     * @param string $content
     * @param string $expectedResult
     * @dataProvider br2nlReturnStringDataProvider
     * @return void
     * @test
     */
    public function br2nlReturnString($content, $expectedResult)
    {
        $this->assertSame($expectedResult, StringUtility::br2nl($content));
    }

    /**
     * Data Provider for getStringLengthReturnInt()
     *
     * @return array
     */
    public function getStringLengthReturnIntDataProvider()
    {
        return [
            [
                'abc',
                3
            ],
            [
                'äbc',
                3
            ],
            [
                "a\nb",
                3
            ],
            [
                "a\r\nb",
                3
            ],
        ];
    }

    /**
     * getStringLength Test
     *
     * @param string $string
     * @param int $expectedResult
     * @dataProvider getStringLengthReturnIntDataProvider
     * @return void
     * @test
     */
    public function getStringLengthReturnInt($string, $expectedResult)
    {
        $this->assertSame($expectedResult, StringUtility::getStringLength($string));
    }
}
