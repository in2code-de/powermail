<?php

namespace In2code\Powermail\Tests\Unit\Domain\Service;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Service\CalculatingCaptchaService;
use In2code\Powermail\Tests\Helper\TestingHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class CalculatingCaptchaServiceTest
 * @coversDefaultClass \In2code\Powermail\Domain\Service\CalculatingCaptchaService
 */
class CalculatingCaptchaServiceTest extends UnitTestCase
{
    /**
     * @var CalculatingCaptchaService
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        TestingHelper::setDefaultConstants();
        $this->generalValidatorMock = $this->getAccessibleMock(
            CalculatingCaptchaService::class,
            null,
            [true]
        );
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'captcha.' => [
                    'default.' => [
                        'image' => 'EXT:powermail/Resources/Private/Image/captcha_bg.png',
                        'font' => 'EXT:powermail/Resources/Private/Fonts/ARCADE.TTF',
                    ],
                ],
            ]
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->generalValidatorMock);
    }

    public static function getColorForCaptchaReturnIntDataProvider(): array
    {
        return [
            [
                '#444444',
                4473924,
            ],
            [
                '#af584c',
                11491404,
            ],
        ];
    }

    /**
     * @param string $hexColorString
     * @param string $expectedResult
     * @dataProvider getColorForCaptchaReturnIntDataProvider
     * @return void
     * @test
     * @covers ::getColorForCaptcha
     */
    public function getColorForCaptchaReturnInt($hexColorString, $expectedResult)
    {
        $imageResource = imagecreatefrompng(
            __DIR__ . '/../../../../Resources/Private/Image/captcha_bg.png'
        );
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'textColor' => $hexColorString,
            ]
        );

        $result = $this->generalValidatorMock->_call('getColorForCaptcha', $imageResource);
        self::assertSame($expectedResult, $result);
    }

    public static function getFontAngleForCaptchaReturnIntDataProvider(): array
    {
        return [
            [
                '-5,5',
                [
                    -5,
                    5,
                ],
            ],
            [
                '0,20',
                [
                    0,
                    20,
                ],
            ],
            [
                '-100,99',
                [
                    -100,
                    99,
                ],
            ],
        ];
    }

    /**
     * @param string $hexColorString
     * @param array $expectedResult
     * @dataProvider getFontAngleForCaptchaReturnIntDataProvider
     * @return void
     * @test
     * @covers ::getFontAngleForCaptcha
     */
    public function getFontAngleForCaptchaReturnInt($hexColorString, $expectedResult)
    {
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'captcha.' => [
                    'default.' => [
                        'textAngle' => $hexColorString,
                    ],
                ],
            ]
        );

        for ($i = 0; $i < 20; $i++) {
            $result = $this->generalValidatorMock->_call('getFontAngleForCaptcha');
            self::assertLessThanOrEqual($expectedResult[1], $result);
            self::assertGreaterThanOrEqual($expectedResult[0], $result);
        }
    }

    public static function getHorizontalDistanceForCaptchaReturnIntDataProvider(): array
    {
        return [
            [
                '-5,5',
                [
                    -5,
                    5,
                ],
            ],
            [
                '0,20',
                [
                    0,
                    20,
                ],
            ],
            [
                '-100,99',
                [
                    -100,
                    99,
                ],
            ],
        ];
    }

    /**
     * @param string $hexColorString
     * @param array $expectedResult
     * @dataProvider getHorizontalDistanceForCaptchaReturnIntDataProvider
     * @return void
     * @test
     * @covers ::getHorizontalDistanceForCaptcha
     */
    public function getHorizontalDistanceForCaptchaReturnInt($hexColorString, $expectedResult)
    {
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'captcha.' => [
                    'default.' => [
                        'distanceHor' => $hexColorString,
                    ],
                ],
            ]
        );

        for ($i = 0; $i < 20; $i++) {
            $result = $this->generalValidatorMock->_call('getHorizontalDistanceForCaptcha');
            self::assertLessThanOrEqual($expectedResult[1], $result);
            self::assertGreaterThanOrEqual($expectedResult[0], $result);
        }
    }

    public static function getVerticalDistanceForCaptchaReturnIntDataProvider(): array
    {
        return [
            [
                '-5,5',
                [
                    -5,
                    5,
                ],
            ],
            [
                '0,20',
                [
                    0,
                    20,
                ],
            ],
            [
                '-100,99',
                [
                    -100,
                    99,
                ],
            ],
        ];
    }

    /**
     * @param string $hexColorString
     * @param array $expectedResult
     * @dataProvider getVerticalDistanceForCaptchaReturnIntDataProvider
     * @return void
     * @test
     * @covers ::getVerticalDistanceForCaptcha
     */
    public function getVerticalDistanceForCaptchaReturnInt($hexColorString, $expectedResult)
    {
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'captcha.' => [
                    'default.' => [
                        'distanceVer' => $hexColorString,
                    ],
                ],
            ]
        );

        for ($i = 0; $i < 20; $i++) {
            $result = $this->generalValidatorMock->_call('getVerticalDistanceForCaptcha');
            self::assertLessThanOrEqual($expectedResult[1], $result);
            self::assertGreaterThanOrEqual($expectedResult[0], $result);
        }
    }

    public static function getStringAndResultForCaptchaReturnsArrayDataProvider(): array
    {
        return [
            [
                '1+3',
                [
                    'result' => 4,
                    'string' => '1 + 3',
                ],
            ],
            [
                '88 + 11',
                [
                    'result' => 99,
                    'string' => '88 + 11',
                ],
            ],
            [
                '12 - 8',
                [
                    'result' => 4,
                    'string' => '12 - 8',
                ],
            ],
            [
                '6:3',
                [
                    'result' => 2,
                    'string' => '6 : 3',
                ],
            ],
            [
                '33x3',
                [
                    'result' => 99,
                    'string' => '33 x 3',
                ],
            ],
        ];
    }

    /**
     * @param string $forceValue
     * @param string $expectedResult
     * @dataProvider getStringAndResultForCaptchaReturnsArrayDataProvider
     * @return void
     * @test
     * @covers ::getStringAndResultForCaptcha
     */
    public function getStringAndResultForCaptchaReturnsArray($forceValue, $expectedResult)
    {
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'forceValue' => $forceValue,
            ]
        );
        $result = $this->generalValidatorMock->_call('getStringAndResultForCaptcha');
        self::assertSame($expectedResult, $result);
    }

    /**
     * @return void
     * @test
     * @covers ::getImagePath
     */
    public function getImagePathReturnString()
    {
        $result = $this->generalValidatorMock->_call('getImagePath');
        self::assertSame('typo3temp/assets/tx_powermail/', $result);

        $this->generalValidatorMock->_set('imagePath', 'typo3temp/');
        $result = $this->generalValidatorMock->_call('getImagePath');
        self::assertSame('typo3temp/', $result);

        $this->generalValidatorMock->_set('imagePath', 'typo3temp/');
        $result = $this->generalValidatorMock->_call('getImagePath', true);
        self::assertSame('/', $result[0]);
        self::assertNotEquals('typo3temp/', $result);
        self::assertStringContainsString('typo3temp/', $result);
    }

    /**
     * @return void
     * @test
     * @covers ::setPathAndFilename
     */
    public function setPathAndFilenameReturnVoid()
    {
        $field = new Field();
        $field->_setProperty('uid', 123);
        $this->generalValidatorMock->_set('imagePath', 'typo3temp/');
        $this->generalValidatorMock->_set('imageFilenamePrefix', 'abc%ddef.png');
        $this->generalValidatorMock->_call('setPathAndFilename', $field);
        self::assertSame('typo3temp/abc123def.png', $this->generalValidatorMock->_get('pathAndFilename'));
    }
}
