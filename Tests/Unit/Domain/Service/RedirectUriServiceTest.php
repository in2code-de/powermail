<?php
namespace In2code\Powermail\Tests\Unit\Domain\Service;

use In2code\Powermail\Tests\Unit\Fixtures\Domain\Service\RedirectUriServiceFixture;
use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class RedirectUriServiceTest
 * @coversDefaultClass \In2code\Powermail\Domain\Service\RedirectUriService
 */
class RedirectUriServiceTest extends UnitTestCase
{

    /**
     * @var RedirectUriServiceFixture
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->initializeTsfe();
        $this->generalValidatorMock = $this->getAccessibleMock(
            RedirectUriServiceFixture::class,
            ['dummy'],
            [new ContentObjectRenderer()]
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
     * Data Provider for getTargetFromFlexFormReturnString()
     *
     * @return array
     */
    public function getTargetFromFlexFormReturnStringDataProvider()
    {
        return [
            '234' => [
                [
                    'settings' => [
                        'flexform' => [
                            'thx' => [
                                'redirect' => '234'
                            ]
                        ]
                    ]
                ],
                '234'
            ],
            'test.jpg' => [
                [
                    'settings' => [
                        'flexform' => [
                            'thx' => [
                                'redirect' => 'fileadmin/test.jpg'
                            ]
                        ]
                    ]
                ],
                'fileadmin/test.jpg'
            ],
            'empty' => [
                [],
                null
            ]
        ];
    }

    /**
     * @param array $flexFormArray
     * @param string $expectedResult
     * @dataProvider getTargetFromFlexFormReturnStringDataProvider
     * @return void
     * @test
     * @covers ::getTargetFromFlexForm
     */
    public function getTargetFromFlexFormReturnString($flexFormArray, $expectedResult)
    {
        $this->generalValidatorMock->_set('flexFormFixture', $flexFormArray);
        $this->assertEquals($expectedResult, $this->generalValidatorMock->_call('getTargetFromFlexForm'));
    }

    /**
     * Data Provider for getTargetFromTypoScriptReturnString()
     *
     * @return array
     */
    public function getTargetFromTypoScriptReturnStringDataProvider()
    {
        return [
            '123' => [
                [
                    'redirect' => 'TEXT',
                    'redirect.' => [
                        'value' => '123'
                    ]
                ],
                '123'
            ],
            'file.pdf' => [
                [
                    'redirect' => 'COA',
                    'redirect.' => [
                        '10' => 'TEXT',
                        '10.' => [
                            'wrap' => 'fileadmin/|',
                            'value' => 'file.pdf'
                        ]
                    ]
                ],
                'fileadmin/file.pdf'
            ],
            'empty' => [
                [],
                null
            ],
        ];
    }

    /**
     * @param array $configuration
     * @param string $expectedResult
     * @dataProvider getTargetFromTypoScriptReturnStringDataProvider
     * @return void
     * @test
     * @covers ::getTargetFromTypoScript
     */
    public function getTargetFromTypoScriptReturnString(array $configuration, $expectedResult)
    {
        $this->generalValidatorMock->_set('typoScriptFixture', $configuration);
        $this->assertEquals($expectedResult, $this->generalValidatorMock->_call('getTargetFromTypoScript'));
    }

    /**
     * Initialize TSFE object
     *
     * @return void
     */
    protected function initializeTsfe()
    {
        $configurationManager = new ConfigurationManager();
        $GLOBALS['TYPO3_CONF_VARS'] = $configurationManager->getDefaultConfiguration();
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['trustedHostsPattern'] = '.*';
        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = [
            'TEXT' => 'TYPO3\CMS\Frontend\ContentObject\TextContentObject',
            'COA' => 'TYPO3\CMS\Frontend\ContentObject\ContentObjectArrayContentObject'
        ];
        $GLOBALS['TT'] = new TimeTracker();
        $GLOBALS['TSFE'] = new TypoScriptFrontendController($GLOBALS['TYPO3_CONF_VARS'], 1, 0, true);
    }
}
