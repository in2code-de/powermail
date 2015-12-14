<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * ConfigurationUtility Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ConfigurationUtilityTest extends UnitTestCase
{

    /**
     * Data Provider for mergeTypoScript2FlexFormReturnsVoid()
     *
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
     * mergeTypoScript2FlexForm Test
     *
     * @param array $settings
     * @param string $level
     * @param array $expectedResult
     * @dataProvider mergeTypoScript2FlexFormReturnsVoidDataProvider
     * @return void
     * @test
     */
    public function mergeTypoScript2FlexFormReturnsVoid($settings, $level, $expectedResult)
    {
        ConfigurationUtility::mergeTypoScript2FlexForm($settings, $level);
        $this->assertSame($expectedResult, $settings);
    }
}
