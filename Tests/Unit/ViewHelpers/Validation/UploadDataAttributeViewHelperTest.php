<?php
namespace In2code\Powermail\Tests\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Request;

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
 * UploadDataAttributeViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class UploadDataAttributeViewHelperTest extends UnitTestCase
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
            '\In2code\Powermail\ViewHelpers\Validation\UploadAttributesViewHelper',
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
     * Dataprovider for renderReturnsArray()
     *
     * @return array
     */
    public function renderReturnsArrayDataProvider()
    {
        return [
            [
                [],
                [],
                [],
                []
            ],
            [
                [],
                [
                    'marker' => 'firstname'
                ],
                [
                    'data-additional' => 'abc'
                ],
                [
                    'data-additional' => 'abc'
                ]
            ],
            [
                [
                    'misc' => [
                        'file' => [
                            'extension' => 'jpg,gif'
                        ]
                    ]
                ],
                [
                    'marker' => 'firstname'
                ],
                [
                    'data-additional' => 'true'
                ],
                [
                    'data-additional' => 'true',
                    'accept' => '.jpg,.gif'
                ]
            ],
            [
                [
                    'misc' => [
                        'file' => [
                            'extension' => 'jpg,gif',
                            'size' => '123456'
                        ]
                    ]
                ],
                [
                    'marker' => 'firstname',
                    'multiselect' => true
                ],
                [
                    'data-additional' => 'true'
                ],
                [
                    'data-additional' => 'true',
                    'multiple' => 'multiple',
                    'accept' => '.jpg,.gif'
                ]
            ],
            [
                [
                    'misc' => [
                        'file' => [
                            'extension' => 'jpg,gif',
                            'size' => '123456'
                        ]
                    ],
                    'validation' => [
                        'client' => '1'
                    ]
                ],
                [
                    'marker' => 'firstname',
                    'multiselect' => true
                ],
                [
                    'data-additional' => 'true'
                ],
                [
                    'data-additional' => 'true',
                    'multiple' => 'multiple',
                    'accept' => '.jpg,.gif',
                    'data-parsley-powermailfilesize' => '123456,firstname',
                    'data-parsley-powermailfilesize-message' => 'validationerror_upload_size',
                    'data-parsley-powermailfileextensions' => 'firstname',
                    'data-parsley-powermailfileextensions-message' => 'validationerror_upload_extension'
                ]
            ],
        ];
    }

    /**
     * Test for render()
     *
     * @param array $settings
     * @param array $fieldProperties
     * @param array $additionalAttributes
     * @param array $expectedResult
     * @return void
     * @dataProvider renderReturnsArrayDataProvider
     * @test
     */
    public function renderReturnsArray($settings, $fieldProperties, $additionalAttributes, $expectedResult)
    {
        $field = new Field();
        foreach ($fieldProperties as $propertyName => $propertyValue) {
            $field->_setProperty($propertyName, $propertyValue);
        }
        $this->abstractValidationViewHelperMock->_set('settings', $settings);
        $result = $this->abstractValidationViewHelperMock->_callRef('render', $field, $additionalAttributes);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider for getDottedListOfExtensions()
     *
     * @return array
     */
    public function getDottedListOfExtensionsReturnsStringDataProvider()
    {
        return [
            [
                'jpg,gif,jpeg',
                '.jpg,.gif,.jpeg'
            ],
            [
                '',
                ''
            ],
            [
                'php',
                '.php'
            ],
            [
                'jpg,gif,jpeg,doc,docx,xls,xlsx',
                '.jpg,.gif,.jpeg,.doc,.docx,.xls,.xlsx'
            ],
        ];
    }

    /**
     * Test for getDottedListOfExtensions()
     *
     * @param string $string
     * @param string $expectedResult
     * @return void
     * @dataProvider getDottedListOfExtensionsReturnsStringDataProvider
     * @test
     */
    public function getDottedListOfExtensionsReturnsString($string, $expectedResult)
    {
        $result = $this->abstractValidationViewHelperMock->_callRef('getDottedListOfExtensions', $string);
        $this->assertSame($expectedResult, $result);
    }
}
