<?php
namespace In2code\Powermail\Tests\Unit\Domain\Service\Mail;

use In2code\Powermail\Domain\Service\Mail\PlaintextService;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class PlaintextServiceTest
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
    public function setUp(): void
    {
        $this->generalValidatorMock = $this->getAccessibleMock(PlaintextService::class, ['addSender']);
    }

    /**
     * @return void
     */
    public function tearDown(): void
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
                "<head>\n\t<title>x</title>\n</head>a<ul><li>b</li><li>c</li></ul>d",
                "a\nb\nc\nd"
            ],
            [
                "<body>\n\t<style>a {color: blue;}</style>\nactual content</body>",
                'actual content'
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
     * @return void
     * @test
     * @covers ::removeInvisibleElements
     */
    public function removeInvisibleElementsReturnString()
    {
        $content = "<b>abc</b><head>\n\t<title>test</title>\n</head><style>\n\ta {color: blue;}\n</style>test<script>\n\talert('hello');\n</script>";
        $expectedResult = '<b>abc</b>test';
        $result = $this->generalValidatorMock->_call('removeInvisibleElements', $content);
        $this->assertSame($expectedResult, $result);
    }

    /**
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
