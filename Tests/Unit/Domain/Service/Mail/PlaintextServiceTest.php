<?php
namespace In2code\Powermail\Tests\Domain\Service\Mail;

use In2code\Powermail\Domain\Service\Mail\PlaintextService;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class PlaintextServiceTest
 * @package In2code\Powermail\Tests\Domain\Service
 * @coversDefaultClass \In2code\Powermail\Domain\Service\Mail\PlaintextService
 */
class PlaintextServiceTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Service\Mail\PlaintextService
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(PlaintextService::class, ['addSender']);
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->generalValidatorMock);
    }

    /**
     * Data Provider for makePlainReturnString()
     *
     * @return array
     */
    public function makePlainReturnStringDataProvider()
    {
        return [
            [
                'a<br>b',
                "a\nb"
            ],
            [
                '<p>test</p><p>test</p>',
                "test\ntest"
            ],
            [
                "<table>\n\t\n<tr><th>a</th><th>b</th></tr><td>\nc</td><td>d</td></table>",
                "a b \nc d"
            ],
            [
                '<h1>t</h1><p>p</p><br>x',
                "t\np\n\nx"
            ],
            [
                'a<ul><li>b</li><li>c</li></ul>d',
                "a\nb\nc\nd"
            ],
            [
                '<head><title>x</title></head>a<ul><li>b</li><li>c</li></ul>d',
                "a\nb\nc\nd"
            ],
            [
                'Please click <a href="http://www.google.com">this</a> link',
                'Please click this [http://www.google.com] link'
            ],
            [
                'Please click <a class="a b href" href="http://www.google.com" id="text" target="_blank">this</a> link',
                'Please click this [http://www.google.com] link'
            ],
            [
                'Please click <a class="a b href" href="http://www.google.com" id="text" target="_blank">this</a> ' .
                    'or <a rel="lightbox" href="https://www.in2code.de" data-foo="bar">this</a> ' .
                    'or <a href="http://www.test.de">this</a> link',
                'Please click this [http://www.google.com] or this [https://www.in2code.de] or ' .
                    'this [http://www.test.de] link'
            ],
            [
                'Please click <a href="http://domain.org/index.php?id=1&amp;x=y">this</a> link',
                'Please click this [http://domain.org/index.php?id=1&x=y] link'
            ],
        ];
    }

    /**
     * makePlain Test
     *
     * @param string $content
     * @param string $expectedResult
     * @dataProvider makePlainReturnStringDataProvider
     * @return void
     * @test
     * @covers ::makePlain
     */
    public function makePlainReturnString($content, $expectedResult)
    {
        $result = $this->generalValidatorMock->_call('makePlain', $content);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * removeHeadElement Test
     *
     * @return void
     * @test
     * @covers ::removeHeadElement
     */
    public function removeHeadElementReturnString()
    {
        $content = '<b>abc</b><head>test</head>test';
        $expectedResult = '<b>abc</b>test';
        $result = $this->generalValidatorMock->_call('removeHeadElement', $content);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * removeLinebreaksAndTabs Test
     *
     * @return void
     * @test
     * @covers ::removeLinebreaksAndTabs
     */
    public function removeLinebreaksAndTabsReturnString()
    {
        $content = "\t\t\r\ntest\t\r\n";
        $expectedResult = 'test';
        $result = $this->generalValidatorMock->_call('removeLinebreaksAndTabs', $content);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * addLineBreaks Test
     *
     * @return void
     * @test
     * @covers ::addLineBreaks
     */
    public function addLineBreaksReturnString()
    {
        $content = '<p>test</p><ul><li>list1</li><li>list1</li></ul>';
        $expectedResult = '<p>test</p><br /></p><br /><li>list1</p><br /><li>list1</p><br /></ul>';
        $result = $this->generalValidatorMock->_call('addLineBreaks', $content);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * addSpaceToTableCells Test
     *
     * @return void
     * @test
     * @covers ::addSpaceToTableCells
     */
    public function addSpaceToTableCellsReturnString()
    {
        $content = '<th>head</th><td>cell</td>';
        $expectedResult = '<th>head</td> <td>cell</td> ';
        $result = $this->generalValidatorMock->_call('addSpaceToTableCells', $content);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * removeTags Test
     *
     * @return void
     * @test
     * @covers ::removeTags
     */
    public function removeTagsReturnString()
    {
        $content = '<a>a</a><b>b</b><br /><address>address</address><div>div</div>';
        $expectedResult = 'ab<br /><address>address</address>div';
        $result = $this->generalValidatorMock->_call('removeTags', $content);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * extractLinkForPlainTextContent Test
     *
     * @return void
     * @test
     * @covers ::extractLinkForPlainTextContent
     */
    public function extractLinkForPlainTextContentReturnString()
    {
        $content = 'Please click <a href="http://domain.org/index.php?id=1&amp;x=y">this</a> link';
        $expectedResult = 'Please click this [http://domain.org/index.php?id=1&x=y] link';
        $result = $this->generalValidatorMock->_call('extractLinkForPlainTextContent', $content);
        $this->assertSame($expectedResult, $result);
    }
}
