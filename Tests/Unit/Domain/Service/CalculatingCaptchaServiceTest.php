<?php
namespace In2code\Powermail\Tests\Domain\Service;

use In2code\Powermail\Domain\Model\Field;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * CalculatingCaptchaService Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class CalculatingCaptchaServiceTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Service\CalculatingCaptchaService
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            '\In2code\Powermail\Domain\Service\CalculatingCaptchaService',
            ['dummy'],
            [true]
        );
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'captcha.' => [
                    'default.' => [
                        'image' => 'EXT:powermail/Resources/Private/Image/captcha_bg.png',
                        'font' => 'EXT:powermail/Resources/Private/Fonts/ARCADE.TTF'
                    ]
                ]
            ]
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
     * Data Provider for getColorForCaptchaReturnInt()
     *
     * @return array
     */
    public function getColorForCaptchaReturnIntDataProvider()
    {
        return [
            [
                '#444444',
                4473924
            ],
            [
                '#af584c',
                11491404
            ]
        ];
    }

    /**
     * getColorForCaptcha Test
     *
     * @param string $hexColorString
     * @param string $expectedResult
     * @dataProvider getColorForCaptchaReturnIntDataProvider
     * @return void
     * @test
     */
    public function getColorForCaptchaReturnInt($hexColorString, $expectedResult)
    {
        $imageResource = ImageCreateFromPNG(
            GeneralUtility::getFileAbsFileName('typo3conf/ext/powermail/Resources/Private/Image/captcha_bg.png')
        );
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'captcha.' => [
                    'default.' => [
                        'textColor' => $hexColorString
                    ]
                ]
            ]
        );

        $result = $this->generalValidatorMock->_call('getColorForCaptcha', $imageResource);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Data Provider for getFontAngleForCaptchaReturnInt()
     *
     * @return array
     */
    public function getFontAngleForCaptchaReturnIntDataProvider()
    {
        return [
            [
                '-5,5',
                [
                    -5,
                    5
                ]
            ],
            [
                '0,20',
                [
                    0,
                    20
                ]
            ],
            [
                '-100,99',
                [
                    -100,
                    99
                ]
            ]
        ];
    }

    /**
     * getFontAngleForCaptcha Test
     *
     * @param string $hexColorString
     * @param array $expectedResult
     * @dataProvider getFontAngleForCaptchaReturnIntDataProvider
     * @return void
     * @test
     */
    public function getFontAngleForCaptchaReturnInt($hexColorString, $expectedResult)
    {
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'captcha.' => [
                    'default.' => [
                        'textAngle' => $hexColorString
                    ]
                ]
            ]
        );

        for ($i = 0; $i < 20; $i++) {
            $result = $this->generalValidatorMock->_call('getFontAngleForCaptcha');
            $this->assertLessThanOrEqual($expectedResult[1], $result);
            $this->assertGreaterThanOrEqual($expectedResult[0], $result);
        }
    }

    /**
     * Data Provider for getHorizontalDistanceForCaptchaReturnInt()
     *
     * @return array
     */
    public function getHorizontalDistanceForCaptchaReturnIntDataProvider()
    {
        return [
            [
                '-5,5',
                [
                    -5,
                    5
                ]
            ],
            [
                '0,20',
                [
                    0,
                    20
                ]
            ],
            [
                '-100,99',
                [
                    -100,
                    99
                ]
            ]
        ];
    }

    /**
     * getHorizontalDistanceForCaptcha Test
     *
     * @param string $hexColorString
     * @param array $expectedResult
     * @dataProvider getHorizontalDistanceForCaptchaReturnIntDataProvider
     * @return void
     * @test
     */
    public function getHorizontalDistanceForCaptchaReturnInt($hexColorString, $expectedResult)
    {
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'captcha.' => [
                    'default.' => [
                        'distanceHor' => $hexColorString
                    ]
                ]
            ]
        );

        for ($i = 0; $i < 20; $i++) {
            $result = $this->generalValidatorMock->_call('getHorizontalDistanceForCaptcha');
            $this->assertLessThanOrEqual($expectedResult[1], $result);
            $this->assertGreaterThanOrEqual($expectedResult[0], $result);
        }
    }

    /**
     * Data Provider for getVerticalDistanceForCaptchaReturnInt()
     *
     * @return array
     */
    public function getVerticalDistanceForCaptchaReturnIntDataProvider()
    {
        return [
            [
                '-5,5',
                [
                    -5,
                    5
                ]
            ],
            [
                '0,20',
                [
                    0,
                    20
                ]
            ],
            [
                '-100,99',
                [
                    -100,
                    99
                ]
            ]
        ];
    }

    /**
     * getVerticalDistanceForCaptcha Test
     *
     * @param string $hexColorString
     * @param array $expectedResult
     * @dataProvider getVerticalDistanceForCaptchaReturnIntDataProvider
     * @return void
     * @test
     */
    public function getVerticalDistanceForCaptchaReturnInt($hexColorString, $expectedResult)
    {
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'captcha.' => [
                    'default.' => [
                        'distanceVer' => $hexColorString
                    ]
                ]
            ]
        );

        for ($i = 0; $i < 20; $i++) {
            $result = $this->generalValidatorMock->_call('getVerticalDistanceForCaptcha');
            $this->assertLessThanOrEqual($expectedResult[1], $result);
            $this->assertGreaterThanOrEqual($expectedResult[0], $result);
        }
    }

    /**
     * Data Provider for getStringAndResultForCaptchaReturnsArray()
     *
     * @return array
     */
    public function getStringAndResultForCaptchaReturnsArrayDataProvider()
    {
        return [
            [
                '1+3',
                [
                    'result' => 4,
                    'string' => '1 + 3'
                ]
            ],
            [
                '88 + 11',
                [
                    'result' => 99,
                    'string' => '88 + 11'
                ]
            ],
            [
                '12 - 8',
                [
                    'result' => 4,
                    'string' => '12 - 8'
                ]
            ],
            [
                '6:3',
                [
                    'result' => 2,
                    'string' => '6 : 3'
                ]
            ],
            [
                '33x3',
                [
                    'result' => 99,
                    'string' => '33 x 3'
                ]
            ],
        ];
    }

    /**
     * getStringAndResultForCaptcha Test
     *
     * @param string $forceValue
     * @param string $expectedResult
     * @dataProvider getStringAndResultForCaptchaReturnsArrayDataProvider
     * @return void
     * @test
     */
    public function getStringAndResultForCaptchaReturnsArray($forceValue, $expectedResult)
    {
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'captcha.' => [
                    'default.' => [
                        'forceValue' => $forceValue
                    ]
                ]
            ]
        );
        $result = $this->generalValidatorMock->_call('getStringAndResultForCaptcha');
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Data Provider for mathematicOperationReturnsInt()
     *
     * @return array
     */
    public function mathematicOperationReturnsIntDataProvider()
    {
        return [
            [
                1,
                3,
                '+',
                4
            ],
            [
                7,
                2,
                '-',
                5
            ],
            [
                6,
                3,
                ':',
                2
            ],
            [
                11,
                3,
                'x',
                33
            ],
        ];
    }

    /**
     * getStringForCaptcha Test
     *
     * @param int $number1
     * @param int $number2
     * @param string $operator
     * @param string $expectedResult
     * @dataProvider mathematicOperationReturnsIntDataProvider
     * @return void
     * @test
     */
    public function mathematicOperationReturnsInt($number1, $number2, $operator, $expectedResult)
    {
        $result = $this->generalValidatorMock->_call('mathematicOperation', $number1, $number2, $operator);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * getImagePath Test
     *
     * @test
     */
    public function getImagePathReturnString()
    {
        $result = $this->generalValidatorMock->_call('getImagePath');
        $this->assertSame('typo3temp/tx_powermail/', $result);

        $this->generalValidatorMock->_set('imagePath', 'typo3temp/');
        $result = $this->generalValidatorMock->_call('getImagePath');
        $this->assertSame('typo3temp/', $result);

        $this->generalValidatorMock->_set('imagePath', 'typo3temp/');
        $result = $this->generalValidatorMock->_call('getImagePath', true);
        $this->assertSame('/', $result[0]);
        $this->assertNotEquals('typo3temp/', $result);
        $this->assertContains('typo3temp/', $result);
    }

    /**
     * setPathAndFilename Test
     *
     * @test
     */
    public function setPathAndFilenameReturnVoid()
    {
        $field = new Field();
        $field->_setProperty('uid', 123);
        $this->generalValidatorMock->_set('imagePath', 'typo3temp/');
        $this->generalValidatorMock->_set('imageFilenamePrefix', 'abc%ddef.png');
        $this->generalValidatorMock->_call('setPathAndFilename', $field);
        $this->assertSame('typo3temp/abc123def.png', $this->generalValidatorMock->_get('pathAndFilename'));
    }
}
