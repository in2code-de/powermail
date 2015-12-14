<?php
namespace In2code\Powermail\Tests\ViewHelpers\String;

use In2code\Powermail\ViewHelpers\String\TrimViewHelper;
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
 * TrimViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class TrimViewHelperTest extends UnitTestCase
{

    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $trimViewHelperMock;

    /**
     * @return void
     */
    public function setUp()
    {
        require_once(dirname(dirname(dirname(__FILE__))) . '/Fixtures/ViewHelpers/String/TrimViewHelperFixture.php');
        $this->trimViewHelperMock = $this->getAccessibleMock(
            '\In2code\Powermail\Tests\Fixtures\ViewHelpers\String\TrimViewHelperFixture',
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->trimViewHelperMock);
    }

    /**
     * Dataprovider for renderReturnsString()
     *
     * @return array
     */
    public function renderReturnsStringDataProvider()
    {
        return [
            [
                ' abc   ',
                'abc',
            ],
            [
                "\t" . 'abc' . "\t",
                'abc',
            ],
            [
                'a' . " \t  " . 'b' . " \t " . 'c',
                'a b c',
            ],
            [
                'a' . "\t\t\t\t" . 'b' . "\t\t\t" . 'c',
                'a b c',
            ],
            [
                '"a" ; "b" ;"c"; "d"',
                '"a";"b";"c";"d"',
            ],
            [
                '<br/><br><br />',
                PHP_EOL . PHP_EOL . PHP_EOL,
            ],
            [
                " \n " . ',' . "\n " . ',' . " \n",
                ', ,',
            ],
        ];
    }

    /**
     * Test for render()
     *
     * @param string $string
     * @param string $expectedResult
     * @return void
     * @dataProvider renderReturnsStringDataProvider
     * @test
     */
    public function renderReturnsString($string, $expectedResult)
    {
        $this->trimViewHelperMock->_set('renderChildrenString', $string);
        $this->assertSame($expectedResult, $this->trimViewHelperMock->_callRef('render'));
    }

    /**
     * Dataprovider for removeDuplicatedWhitespaceReturnsString()
     *
     * @return array
     */
    public function removeDuplicatedWhitespaceReturnsStringDataProvider()
    {
        return [
            [
                '  abc    ',
                ' abc ',
            ],
            [
                'a' . PHP_EOL . PHP_EOL . 'b',
                'a b',
            ],
            [
                "\t\na\t\n",
                ' a ',
            ]
        ];
    }

    /**
     * Test for removeDuplicatedWhitespace()
     *
     * @param string $string
     * @param string $expectedResult
     * @return void
     * @dataProvider removeDuplicatedWhitespaceReturnsStringDataProvider
     * @test
     */
    public function removeDuplicatedWhitespaceReturnsString($string, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->trimViewHelperMock->_callRef('removeDuplicatedWhitespace', $string));
    }
}
