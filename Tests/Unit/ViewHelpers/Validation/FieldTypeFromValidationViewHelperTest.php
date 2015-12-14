<?php
namespace In2code\Powermail\Tests\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
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
 * FieldTypeFromValidationViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class FieldTypeFromValidationViewHelperTest extends UnitTestCase
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
            '\In2code\Powermail\ViewHelpers\Validation\FieldTypeFromValidationViewHelper',
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
     * Dataprovider for render()
     *
     * @return array
     */
    public function renderReturnsStringDataProvider()
    {
        return [
            'defaultWithHtml5' => [
                0,
                'text',
                true
            ],
            'defaultWithoutHtml5' => [
                0,
                'text',
                false
            ],
            'emailValidationWithoutHtml5' => [
                1,
                'text',
                false
            ],
            'emailValidationWithHtml5' => [
                1,
                'email',
                true
            ],
            'urlValidationWithoutHtml5' => [
                2,
                'text',
                false
            ],
            'urlValidationWithHtml5' => [
                2,
                'url',
                true
            ],
            'telValidationWithoutHtml5' => [
                3,
                'text',
                false
            ],
            'telValidationWithHtml5' => [
                3,
                'tel',
                true
            ],
            'numberValidationWithoutHtml5' => [
                4,
                'text',
                false
            ],
            'numberValidationWithHtml5' => [
                4,
                'number',
                true
            ],
            'rangeValidationWithoutHtml5' => [
                8,
                'text',
                false
            ],
            'rangeValidationWithHtml5' => [
                8,
                'range',
                true
            ],
        ];
    }

    /**
     * Test for render()
     *
     * @param string $validation
     * @param string $expectedResult
     * @param bool $nativeValidationEnabled
     * @return void
     * @dataProvider renderReturnsStringDataProvider
     * @test
     */
    public function renderReturnsString($validation, $expectedResult, $nativeValidationEnabled)
    {
        $this->abstractValidationViewHelperMock->_set(
            'settings',
            [
                'validation' => [
                    'native' => ($nativeValidationEnabled ? '1' : '0')
                ]
            ]
        );
        $field = new Field;
        $field->setValidation($validation);

        $result = $this->abstractValidationViewHelperMock->_callRef('render', $field);
        $this->assertSame($expectedResult, $result);
    }

}
