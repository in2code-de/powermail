<?php
namespace In2code\Powermail\Tests\ViewHelpers\Misc;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\ViewHelpers\Misc\PrefillFieldViewHelper;
use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class PrefillFieldViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Misc\PrefillFieldViewHelper
 */
class PrefillFieldViewHelperTest extends UnitTestCase
{

    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $abstractValidationViewHelperMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->abstractValidationViewHelperMock = $this->getAccessibleMock(
            PrefillFieldViewHelper::class,
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
     * Dataprovider for getDefaultValueReturnsString()
     *
     * @return array
     */
    public function getDefaultValueReturnsStringDataProvider()
    {
        return [
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ],
                [
                    'field' => [
                        'marker' => 'abc',
                        '123' => 'ghi'
                    ],
                    'marker' => 'def',
                    'uid123' => 'jkl'
                ],
                [
                    'prefill.' => [
                        'marker' => 'pqr'
                    ]
                ],
                'abc'
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ],
                [
                    'field' => [
                        '123' => 'ghi'
                    ],
                    'marker' => 'def',
                    'uid123' => 'jkl'
                ],
                [
                    'prefill.' => [
                        'marker' => 'pqr'
                    ]
                ],
                'def'
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ],
                [
                    'field' => [
                        '123' => 'ghi'
                    ],
                    'uid123' => 'jkl'
                ],
                [
                    'prefill.' => [
                        'marker' => 'pqr'
                    ]
                ],
                'ghi'
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ],
                [
                    'uid123' => 'jkl'
                ],
                [
                    'prefill.' => [
                        'marker' => 'pqr'
                    ]
                ],
                'jkl'
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ],
                [],
                [
                    'prefill.' => [
                        'marker' => 'pqr'
                    ]
                ],
                'mno'
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ],
                [],
                [],
                'mno'
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker'
                ],
                [],
                [
                    'prefill.' => [
                        'marker' => 'pqr'
                    ]
                ],
                'pqr'
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ],
                [
                    'field' => [
                        'marker' => '',
                        '123' => 'ghi'
                    ],
                    'marker' => 'def',
                    'uid123' => 'jkl'
                ],
                [
                    'prefill.' => [
                        'marker' => 'pqr'
                    ]
                ],
                'def'
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                ],
                [],
                [],
                ''
            ],
        ];
    }

    /**
     * @param array $fieldValues
     * @param array $piVars
     * @param array $configuration
     * @param string $expectedResult
     * @return void
     * @dataProvider getDefaultValueReturnsStringDataProvider
     * @test
     * @covers ::render
     * @covers ::getValue
     * @covers ::buildValue
     */
    public function getDefaultValueReturnsString($fieldValues, $piVars, $configuration, $expectedResult)
    {
        $field = new Field();
        foreach ($fieldValues as $name => $value) {
            $field->_setProperty($name, $value);
        }
        $this->abstractValidationViewHelperMock->_set('contentObject', new ContentObjectRenderer());
        $this->abstractValidationViewHelperMock->_set('piVars', $piVars);
        $this->abstractValidationViewHelperMock->_set('configuration', $configuration);
        $this->abstractValidationViewHelperMock->_set('field', $field);
        $this->abstractValidationViewHelperMock->_set('marker', $field->getMarker());
        $this->abstractValidationViewHelperMock->_callRef('buildValue');
        $this->assertSame($expectedResult, $this->abstractValidationViewHelperMock->_callRef('getValue'));
    }

    /**
     * Dataprovider for getDefaultValueReturnsString()
     *
     * @return array
     */
    public function getFromTypoScriptContentObjectReturnsStringDataProvider()
    {
        return [
            [
                [
                    'prefill.' => [
                        'marker' => 'TEXT',
                        'marker.' => [
                            'value' => 'y',
                            'wrap' => 'x|z'
                        ]
                    ]
                ],
                'marker',
                'xyz'
            ],
            [
                [
                    'prefill.' => [
                        'email' => 'TEXT',
                        'email.' => [
                            'data' => 'date:U',
                            'strftime' => '%d.%m.%Y %H:%M'
                        ]
                    ]
                ],
                'email',
                (string) strftime('%d.%m.%Y %H:%M')
            ],
        ];
    }

    /**
     * @param array $configuration
     * @param string $marker
     * @param string $expectedResult
     * @return void
     * @dataProvider getFromTypoScriptContentObjectReturnsStringDataProvider
     * @test
     * @covers ::getFromTypoScriptContentObject
     */
    public function getFromTypoScriptContentObjectReturnsString(array $configuration, $marker, $expectedResult)
    {
        $this->initializeTsfe();
        $this->abstractValidationViewHelperMock->_set('configuration', $configuration);
        $field = new Field();
        $field->setMarker($marker);
        $this->abstractValidationViewHelperMock->_set('field', $field);
        $this->abstractValidationViewHelperMock->_set('marker', $marker);
        $this->abstractValidationViewHelperMock->_set('contentObject', new ContentObjectRenderer());
        $value = '';
        $this->assertSame(
            $expectedResult,
            $this->abstractValidationViewHelperMock->_callRef('getFromTypoScriptContentObject', $value)
        );
    }

    /**
     * Dataprovider for getFromTypoScriptRawReturnsString()
     *
     * @return array
     */
    public function getFromTypoScriptRawReturnsStringDataProvider()
    {
        return [
            [
                [
                    'prefill.' => [
                        'email' => 'abcdef'
                    ]
                ],
                'email',
                'abcdef'
            ],
            [
                [
                    'prefill.' => [
                        'email' => 'TEXT',
                        'email.' => [
                            'value' => 'xyz'
                        ]
                    ]
                ],
                'email',
                ''
            ],
            [
                [
                    'prefill.' => [
                        'marker' => 'TEXT'
                    ]
                ],
                'marker',
                'TEXT'
            ],
        ];
    }

    /**
     * @param array $configuration
     * @param string $marker
     * @param string $expectedResult
     * @return void
     * @dataProvider getFromTypoScriptRawReturnsStringDataProvider
     * @test
     * @covers ::getFromTypoScriptRaw
     */
    public function getFromTypoScriptRawReturnsString(array $configuration, $marker, $expectedResult)
    {
        $this->abstractValidationViewHelperMock->_set('configuration', $configuration);
        $this->abstractValidationViewHelperMock->_set('marker', $marker);
        $value = '';
        $this->assertSame(
            $expectedResult,
            $this->abstractValidationViewHelperMock->_callRef('getFromTypoScriptRaw', $value)
        );
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
            'TEXT' => 'TYPO3\CMS\Frontend\ContentObject\TextContentObject'
        ];
        $GLOBALS['TT'] = new TimeTracker();
        $GLOBALS['TSFE'] = new TypoScriptFrontendController($GLOBALS['TYPO3_CONF_VARS'], 1, 0, true);
    }
}
