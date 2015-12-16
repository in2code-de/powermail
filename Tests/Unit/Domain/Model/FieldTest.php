<?php
namespace In2code\Powermail\Tests\Domain\Model;

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
 * Field Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class FieldTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Tests\Fixtures\Domain\Model\FieldFixture
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        require_once(dirname(dirname(dirname(__FILE__))) . '/Fixtures/Domain/Model/FieldFixture.php');
        $this->generalValidatorMock = $this->getAccessibleMock(
            '\In2code\Powermail\Tests\Fixtures\Domain\Model\FieldFixture',
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->generalValidatorMock);
    }

    /**
     * Dataprovider optionArrayReturnsArray()
     *
     * @return array
     */
    public function optionArrayReturnsArrayDataProvider()
    {
        return [
            [
                'abc',
                [
                    [
                        'label' => 'abc',
                        'value' => 'abc',
                        'selected' => 0
                    ],
                ]
            ],
            [
                "red\nblue\nyellow",
                [
                    [
                        'label' => 'red',
                        'value' => 'red',
                        'selected' => 0
                    ],
                    [
                        'label' => 'blue',
                        'value' => 'blue',
                        'selected' => 0
                    ],
                    [
                        'label' => 'yellow',
                        'value' => 'yellow',
                        'selected' => 0
                    ],
                ]
            ],
            [
                "please choose...|\nred\nblue|blue|*",
                [
                    [
                        'label' => 'please choose...',
                        'value' => '',
                        'selected' => 0
                    ],
                    [
                        'label' => 'red',
                        'value' => 'red',
                        'selected' => 0
                    ],
                    [
                        'label' => 'blue',
                        'value' => 'blue',
                        'selected' => 1
                    ],
                ]
            ],
            [
                "||*\nred|red shoes",
                [
                    [
                        'label' => '',
                        'value' => '',
                        'selected' => 1
                    ],
                    [
                        'label' => 'red',
                        'value' => 'red shoes',
                        'selected' => 0
                    ],
                ]
            ],
            [
                "Red Shoes | 1 \nBlack Shoes | 2 | *\nBlue Shoes | ",
                [
                    [
                        'label' => 'Red Shoes',
                        'value' => '1',
                        'selected' => 0
                    ],
                    [
                        'label' => 'Black Shoes',
                        'value' => '2',
                        'selected' => 1
                    ],
                    [
                        'label' => 'Blue Shoes',
                        'value' => '',
                        'selected' => 0
                    ],
                ]
            ],
        ];
    }

    /**
     * Test for optionArray()
     *
     * @param string $value
     * @param array $expectedResult
     * @return void
     * @dataProvider optionArrayReturnsArrayDataProvider
     * @test
     */
    public function optionArrayReturnsArray($value, $expectedResult)
    {
        $result = $this->generalValidatorMock->_call('optionArray', $value, '', false);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider dataTypeFromFieldTypeReturnsString()
     *
     * @return array
     */
    public function dataTypeFromFieldTypeReturnsStringDataProvider()
    {
        return [
            [
                'captcha',
                0
            ],
            [
                'check',
                1
            ],
            [
                'file',
                3
            ],
            [
                'input',
                0
            ],
            [
                'textarea',
                0
            ],
            [
                'select',
                0
            ],
            [
                'select',
                1,
                true
            ],
        ];
    }

    /**
     * Test for dataTypeFromFieldType()
     *
     * @param string $fieldType
     * @param array $expectedResult
     * @param bool $multiple
     * @return void
     * @dataProvider dataTypeFromFieldTypeReturnsStringDataProvider
     * @test
     */
    public function dataTypeFromFieldTypeReturnsString($fieldType, $expectedResult, $multiple = false)
    {
        if ($multiple) {
            $this->generalValidatorMock->_set('multiselect', $multiple);
        }
        $result = $this->generalValidatorMock->_call('dataTypeFromFieldType', $fieldType);
        $this->assertSame($expectedResult, $result);
    }
}
