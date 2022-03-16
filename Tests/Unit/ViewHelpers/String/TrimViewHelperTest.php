<?php
namespace In2code\Powermail\Tests\Unit\ViewHelpers\String;

use In2code\Powermail\Tests\Unit\Fixtures\ViewHelpers\String\TrimViewHelperFixture;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class TrimViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\String\TrimViewHelper
 */
class TrimViewHelperTest extends UnitTestCase
{

    /**
     * @var TrimViewHelperFixture
     */
    protected $trimViewHelperMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->trimViewHelperMock = $this->getAccessibleMock(
            TrimViewHelperFixture::class,
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
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
                '',
            ],
            [
                " \n " . ',' . "\n " . ',' . " \n",
                ', ,',
            ],
            [
                ' "name" ;"firstname" ;  "email"; <br>    <br>    <br> <br> ' . "\t\t\n\n" .
                    ' "name";"firstname";"email"; <br />' .
                    '   "name";"email";   ',
                '"name";"firstname";"email";' . PHP_EOL .
                    '"name";"firstname";"email";' . PHP_EOL .
                    '"name";"email";',
            ],
        ];
    }

    /**
     * @param string $string
     * @param string $expectedResult
     * @return void
     * @dataProvider renderReturnsStringDataProvider
     * @test
     * @covers ::render
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
     * @param string $string
     * @param string $expectedResult
     * @return void
     * @dataProvider removeDuplicatedWhitespaceReturnsStringDataProvider
     * @test
     * @covers ::removeDuplicatedWhitespace
     */
    public function removeDuplicatedWhitespaceReturnsString($string, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->trimViewHelperMock->_callRef('removeDuplicatedWhitespace', $string));
    }
}
