<?php
namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Tests\Helper\TestingHelper;
use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class ConfigurationUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\ConfigurationUtility
 */
class ConfigurationUtilityTest extends UnitTestCase
{

    /**
     * @var array
     */
    protected $testFilesToDelete = [];

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isDisableIpLogActive
     * @covers ::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTypo3ConfigurationVariables
     */
    public function isDisableIpLogActiveReturnsBool()
    {
        $configuration = [
            'disableIpLog' => '1'
        ];
        // @extensionScannerIgnoreLine
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail'] = serialize($configuration);
        $this->assertTrue(ConfigurationUtility::isDisableIpLogActive());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isDisableMarketingInformationActive
     * @covers ::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTypo3ConfigurationVariables
     */
    public function isDisableMarketingInformationActiveReturnsBool()
    {
        $configuration = [
            'disableMarketingInformation' => '1'
        ];
        // @extensionScannerIgnoreLine
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail'] = serialize($configuration);
        $this->assertTrue(ConfigurationUtility::isDisableMarketingInformationActive());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isDisableBackendModuleActive
     * @covers ::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTypo3ConfigurationVariables
     */
    public function isDisableBackendModuleActiveReturnsBool()
    {
        $configuration = [
            'disableBackendModule' => '1'
        ];
        // @extensionScannerIgnoreLine
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail'] = serialize($configuration);
        $this->assertTrue(ConfigurationUtility::isDisableBackendModuleActive());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isDisablePluginInformationActive
     * @covers ::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTypo3ConfigurationVariables
     */
    public function isDisablePluginInformationActiveReturnsBool()
    {
        $configuration = [
            'disablePluginInformation' => '1'
        ];
        // @extensionScannerIgnoreLine
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail'] = serialize($configuration);
        $this->assertTrue(ConfigurationUtility::isDisablePluginInformationActive());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isDisablePluginInformationMailPreviewActive
     * @covers ::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTypo3ConfigurationVariables
     */
    public function isDisablePluginInformationMailPreviewActiveReturnsBool()
    {
        $configuration = [
            'disablePluginInformationMailPreview' => '1'
        ];
        // @extensionScannerIgnoreLine
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail'] = serialize($configuration);
        $this->assertTrue(ConfigurationUtility::isDisablePluginInformationMailPreviewActive());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isEnableCachingActive
     * @covers ::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTypo3ConfigurationVariables
     */
    public function isEnableCachingActiveReturnsBool()
    {
        $configuration = [
            'enableCaching' => '1'
        ];
        // @extensionScannerIgnoreLine
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail'] = serialize($configuration);
        $this->assertTrue(ConfigurationUtility::isEnableCachingActive());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isReplaceIrreWithElementBrowserActive
     * @covers ::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTypo3ConfigurationVariables
     */
    public function isReplaceIrreWithElementBrowserActiveReturnsBool()
    {
        $configuration = [
            'replaceIrreWithElementBrowser' => '1'
        ];
        // @extensionScannerIgnoreLine
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail'] = serialize($configuration);
        $this->assertTrue(ConfigurationUtility::isReplaceIrreWithElementBrowserActive());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isL10nModeMergeActive
     * @covers ::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getExtensionConfiguration
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTypo3ConfigurationVariables
     */
    public function isL10nModeMergeActiveReturnsBool()
    {
        $configuration = [
            'l10n_mode_merge' => '1'
        ];
        // @extensionScannerIgnoreLine
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail'] = serialize($configuration);
        $this->assertTrue(ConfigurationUtility::isL10nModeMergeActive());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::getDefaultMailFromAddress
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTypo3ConfigurationVariables
     */
    public function getDefaultMailFromAddressReturnsString()
    {
        $testString1 = 'test@mail.org';
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] = '';
        $this->assertSame($testString1, ConfigurationUtility::getDefaultMailFromAddress($testString1));

        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] = '';
        $this->assertEmpty(ConfigurationUtility::getDefaultMailFromAddress());

        $testString2 = 'test@mail.com';
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] = $testString2;
        $this->assertSame($testString2, ConfigurationUtility::getDefaultMailFromAddress());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::getDefaultMailFromName
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTypo3ConfigurationVariables
     */
    public function getDefaultMailFromNameReturnsString()
    {
        $testString = 'randomName';
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'] = $testString;
        $this->assertSame($testString, ConfigurationUtility::getDefaultMailFromName());

        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'] = '';
        $this->assertEmpty(ConfigurationUtility::getDefaultMailFromName());
    }

    /**
     * @return void
     * @test
     * @covers ::getIconPath
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTypo3ConfigurationVariables
     */
    public function getIconPathReturnsString()
    {
        $icon = 'random';
        $this->assertSame('EXT:powermail/Resources/Public/Icons/' . $icon, ConfigurationUtility::getIconPath($icon));
    }

    /**
     * @return void
     * @test
     * @covers ::isValidationEnabled
     */
    public function isValidationEnabledReturnsBool()
    {
        $settings = [
            'spamshield' => [
                '_enable' => '1',
                'methods' => [
                    [
                        'class' => 'anyClass',
                        '_enable' => '1'
                    ]
                ]
            ]
        ];
        $this->assertTrue(ConfigurationUtility::isvalidationenabled($settings, 'anyClass'));
    }

    /**
     * @return array
     */
    public function mergeTypoScript2FlexFormReturnsVoidDataProvider()
    {
        return [
            'empty' => [
                [],
                '',
                []
            ],
            'simple' => [
                [
                    'setup' => [
                        'abc' => 'def'
                    ],
                    'flexform' => [
                        'ghi' => 'jkl'
                    ]
                ],
                'setup',
                [
                    'abc' => 'def',
                    'ghi' => 'jkl',
                ]
            ],
            'override settings with flexform - level 1' => [
                [
                    'setup' => [
                        'main' => [
                            'pid' => '124'
                        ]
                    ],
                    'flexform' => [
                        'main' => [
                            'pid' => '123'
                        ]
                    ]
                ],
                'setup',
                [
                    'main' => [
                        'pid' => '123'
                    ]
                ]
            ],
            'override flexform only if not empty' => [
                [
                    'setup' => [
                        'prop1' => 'val1',
                        'prop2' => 'val2'
                    ],
                    'flexform' => [
                        'prop1' => '',
                        'prop2' => 'val3'
                    ]
                ],
                'setup',
                [
                    'prop1' => 'val1',
                    'prop2' => 'val3'
                ]
            ],
            'override flexform only if not empty - level 2' => [
                [
                    'setup' => [
                        'prop1' => [
                            'prop11' => 'val1',
                            'prop12' => 'val2'
                        ],
                    ],
                    'flexform' => [
                        'prop1' => [
                            'prop11' => '',
                            'prop12' => 'val3'
                        ],
                    ]
                ],
                'setup',
                [
                    'prop1' => [
                        'prop11' => 'val1',
                        'prop12' => 'val3'
                    ],
                ]
            ],
            'complex' => [
                [
                    'setup' => [
                        'receiver' => [
                            'mailformat' => 'html',
                            'default' => [
                                'senderName' => 'TEXT',
                                'senderName.' => [
                                    'value' => 'abc'
                                ],
                            ]
                        ],
                        'captcha' => [
                            'default' => [
                                'image' => 'abc.jpg'
                            ]
                        ]
                    ],
                    'flexform' => [
                        'receiver' => [
                            'mailformat' => ''
                        ],
                        'captcha' => [
                            'default' => [
                                'image' => 'def.jpg'
                            ]
                        ]
                    ]
                ],
                'setup',
                [
                    'receiver' => [
                        'mailformat' => 'html',
                        'default' => [
                            'senderName' => 'TEXT',
                            'senderName.' => [
                                'value' => 'abc'
                            ],
                        ]
                    ],
                    'captcha' => [
                        'default' => [
                            'image' => 'def.jpg'
                        ]
                    ]
                ]
            ],
            'Pi2' => [
                [
                    'setup' => [
                        'prop' => 'props'
                    ],
                    'Pi2' => [
                        'prop' => 'propp'
                    ]
                ],
                'Pi2',
                [
                    'prop' => 'propp'
                ]
            ],
        ];
    }

    /**
     * @param array $settings
     * @param string $level
     * @param array $expectedResult
     * @dataProvider mergeTypoScript2FlexFormReturnsVoidDataProvider
     * @return void
     * @covers ::mergeTypoScript2FlexForm
     * @covers \In2code\Powermail\Utility\ArrayUtility::arrayMergeRecursiveOverrule
     */
    public function testMergeTypoScript2FlexFormReturnsVoid($settings, $level, $expectedResult)
    {
        ConfigurationUtility::mergeTypoScript2FlexForm($settings, $level);
        $this->assertSame($expectedResult, $settings);
    }
}
