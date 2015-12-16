<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\ArrayUtility;
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
 * Array Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ArrayUtilityTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Utility\ArrayUtility
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock('\In2code\Powermail\Utility\ArrayUtility', ['dummy']);
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->generalValidatorMock);
    }

    /**
     * getAbcArray Test
     *
     * @return void
     * @test
     */
    public function getAbcArrayReturnsArray()
    {
        $this->assertSame(
            [
                'A',
                'B',
                'C',
                'D',
                'E',
                'F',
                'G',
                'H',
                'I',
                'J',
                'K',
                'L',
                'M',
                'N',
                'O',
                'P',
                'Q',
                'R',
                'S',
                'T',
                'U',
                'V',
                'W',
                'X',
                'Y',
                'Z'
            ],
            ArrayUtility::getAbcArray()
        );
    }

    /**
     * Data Provider for isJsonArrayReturnsBool()
     *
     * @return array
     */
    public function isJsonArrayReturnsBoolDataProvider()
    {
        return [
            [
                json_encode(['a']),
                true
            ],
            [
                json_encode('a,b:c'),
                false
            ],
            [
                json_encode(['object' => 'a']),
                true
            ],
            [
                json_encode([['title' => 'test2'], ['title' => 'test2']]),
                true
            ],
            [
                'a,b:c',
                false
            ],
        ];
    }

    /**
     * isJsonArray Test
     *
     * @param string $value
     * @param bool $expectedResult
     * @dataProvider isJsonArrayReturnsBoolDataProvider
     * @return void
     * @test
     */
    public function isJsonArrayReturnsBool($value, $expectedResult)
    {
        $this->assertSame($expectedResult, ArrayUtility::isJsonArray($value));
    }

    /**
     * Data Provider for htmlspecialcharsOnArrayReturnsArray()
     *
     * @return array
     */
    public function htmlspecialcharsOnArrayReturnsArrayDataProvider()
    {
        return [
            [
                [
                    '<te&st>'
                ],
                [
                    '&lt;te&amp;st&gt;'
                ]
            ],
            [
                [
                    '<test>',
                    [
                        '<test>' => '<test>'
                    ]
                ],
                [
                    '&lt;test&gt;',
                    [
                        '&lt;test&gt;' => '&lt;test&gt;'
                    ]
                ]
            ],
        ];
    }

    /**
     * htmlspecialcharsOnArray Test
     *
     * @param array $array
     * @param array $expectedResult
     * @dataProvider htmlspecialcharsOnArrayReturnsArrayDataProvider
     * @return void
     * @test
     */
    public function htmlspecialcharsOnArrayReturnsArray($array, $expectedResult)
    {
        $this->assertSame($expectedResult, ArrayUtility::htmlspecialcharsOnArray($array));
    }
}
