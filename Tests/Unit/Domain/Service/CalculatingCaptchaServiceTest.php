<?php
namespace In2code\Powermail\Tests\Unit\Domain\Service;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Service\CalculatingCaptchaService;
use In2code\Powermail\Utility\SessionUtility;
use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class CalculatingCaptchaServiceTest
 * @coversDefaultClass \In2code\Powermail\Domain\Service\CalculatingCaptchaService
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
            CalculatingCaptchaService::class,
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
     * Data Provider for validCodeReturnsBool
     *
     * @return array
     */
    public function validCodeReturnsBoolDataProvider()
    {
        return [
            [
                '123',
                '123',
                true
            ],
            [
                '1234',
                '123',
                false
            ],
            [
                '0',
                '0',
                false
            ],
            [
                '',
                '',
                false
            ],
            [
                'test',
                'test',
                false
            ],
            [
                'a123',
                'a123',
                false
            ],
            [
                'a123',
                '',
                false
            ],
            [
                '123a',
                '',
                false
            ],
            [
                '',
                null,
                false
            ],
            [
                null,
                null,
                false
            ],
            [
                false,
                false,
                false
            ]
        ];
    }

    /**
     * @param string $code Given from input field (should be a string)
     * @param string $codeInSession (string or empty)
     * @param bool $expectedResult
     * @dataProvider validCodeReturnsBoolDataProvider
     * @test
     * @covers ::validCode
     */
    public function validCodeReturnsBool($code, $codeInSession, $expectedResult)
    {
        $this->initializeTsfe();
        $field = new Field();
        $field->_setProperty('uid', 123);
        SessionUtility::setCaptchaSession($codeInSession, 123);
        $this->assertSame($expectedResult, $this->generalValidatorMock->_call('validCode', $code, $field, false));
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
     * @param string $hexColorString
     * @param string $expectedResult
     * @dataProvider getColorForCaptchaReturnIntDataProvider
     * @return void
     * @test
     * @covers ::getColorForCaptcha
     */
    public function getColorForCaptchaReturnInt($hexColorString, $expectedResult)
    {
        $imageResource = ImageCreateFromPNG(
            GeneralUtility::getFileAbsFileName('typo3conf/ext/powermail/Resources/Private/Image/captcha_bg.png')
        );
        $this->generalValidatorMock->_set(
            'configuration',
            [
                'textColor' => $hexColorString
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
                'forceValue' => $forceValue
            ]
        );
        $result = $this->generalValidatorMock->_call('getStringAndResultForCaptcha');
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return void
     * @test
     * @covers ::getImagePath
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
        $this->assertSame('typo3temp/abc123def.png', $this->generalValidatorMock->_get('pathAndFilename'));
    }

    /**
     * Initialize TSFE object
     *
     * @return void
     */
    protected function initializeTsfe()
    {
        $_SERVER['REMOTE_ADDR'] = '';
        $configurationManager = new ConfigurationManager();
        $GLOBALS['TYPO3_CONF_VARS'] = $configurationManager->getDefaultConfiguration();
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['trustedHostsPattern'] = '.*';
        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = [
            'TEXT' => 'TYPO3\CMS\Frontend\ContentObject\TextContentObject',
            'COA' => 'TYPO3\CMS\Frontend\ContentObject\ContentObjectArrayContentObject'
        ];
        $GLOBALS['TT'] = new TimeTracker();
        $GLOBALS['TSFE'] = new TypoScriptFrontendController($GLOBALS['TYPO3_CONF_VARS'], 1, 0, true);
        $GLOBALS['TSFE']->fe_user = new FrontendUserAuthentication();
    }
}
