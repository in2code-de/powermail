<?php
namespace In2code\Powermail\Tests\ViewHelpers\Condition;

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
 * IsNotExcludedFromPowermailAllViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class IsNotExcludedFromPowermailAllViewHelperTest extends UnitTestCase
{

    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $isNotExcludedFromPowermailAllViewHelperMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->isNotExcludedFromPowermailAllViewHelperMock = $this->getAccessibleMock(
            '\In2code\Powermail\ViewHelpers\Condition\IsNotExcludedFromPowermailAllViewHelper',
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->isNotExcludedFromPowermailAllViewHelperMock);
    }

    /**
     * Dataprovider for getExcludedValuesReturnArray()
     *
     * @return array
     */
    public function getExcludedValuesReturnArrayDataProvider()
    {
        return [
            [
                'createAction',
                [
                    'excludeFromPowermailAllMarker' => [
                        'submitPage' => [
                            'excludeFromFieldTypes' => 'hidden, captcha, input'
                        ]
                    ]
                ],
                'excludeFromFieldTypes',
                [
                    'hidden',
                    'captcha',
                    'input'
                ]
            ],
            [
                'confirmationAction',
                [
                    'excludeFromPowermailAllMarker' => [
                        'confirmationPage' => [
                            'excludeFromFieldTypes' => 'hidden, input'
                        ]
                    ]
                ],
                'excludeFromFieldTypes',
                [
                    'hidden',
                    'input'
                ]
            ],
            [
                'sender',
                [
                    'excludeFromPowermailAllMarker' => [
                        'senderMail' => [
                            'excludeFromMarkerNames' => 'abc, daafsd',
                            'excludeFromFieldTypes' => 'hidden, captcha'
                        ]
                    ]
                ],
                'excludeFromFieldTypes',
                [
                    'hidden',
                    'captcha'
                ]
            ],
            [
                'receiver',
                [
                    'excludeFromPowermailAllMarker' => [
                        'receiverMail' => [
                            'excludeFromMarkerNames' => 'email, firstname',
                            'excludeFromFieldTypes' => 'hidden, input'
                        ]
                    ]
                ],
                'excludeFromMarkerNames',
                [
                    'email',
                    'firstname'
                ]
            ],
            [
                'optin',
                [
                    'excludeFromPowermailAllMarker' => [
                        'optinMail' => [
                            'excludeFromMarkerNames' => 'email, firstname',
                            'excludeFromFieldTypes' => 'hidden, input'
                        ]
                    ]
                ],
                'excludeFromMarkerNames',
                [
                    'email',
                    'firstname'
                ]
            ],
            [
                'optin',
                [
                    'excludeFromPowermailAllMarker' => [
                        'optinMail' => [
                            'excludeFromMarkerNames' => 'email, firstname',
                            'excludeFromFieldTypes' => 'hidden, input'
                        ]
                    ]
                ],
                'excludeFromFieldTypes',
                [
                    'hidden',
                    'input'
                ]
            ],
        ];
    }

    /**
     * Test for render()
     *
     * @param string $type
     * @param array $settings
     * @param string $configurationType
     * @param array $expectedResult
     * @return void
     * @dataProvider getExcludedValuesReturnArrayDataProvider
     * @test
     */
    public function getExcludedValuesReturnArray($type, $settings, $configurationType, $expectedResult)
    {
        $result = $this->isNotExcludedFromPowermailAllViewHelperMock->_callRef(
            'getExcludedValues',
            $type,
            $settings,
            $configurationType
        );
        $this->assertSame($expectedResult, $result);
    }

}
