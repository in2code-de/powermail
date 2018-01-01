<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\ArrayUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class ArrayUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\ArrayUtility
 */
class ArrayUtilityTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Utility\ArrayUtility
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(ArrayUtility::class, ['dummy']);
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->generalValidatorMock);
    }

    /**
     * @return void
     * @test
     * @covers ::getAbcArray
     */
    public function getAbcArrayReturnsArray()
    {
        $this->assertSame(
            [
                'A',
                'B',
                'C',
                'D',
                'E',
                'F',
                'G',
                'H',
                'I',
                'J',
                'K',
                'L',
                'M',
                'N',
                'O',
                'P',
                'Q',
                'R',
                'S',
                'T',
                'U',
                'V',
                'W',
                'X',
                'Y',
                'Z'
            ],
            ArrayUtility::getAbcArray()
        );
    }

    /**
     * Data Provider for isJsonArrayReturnsBool()
     *
     * @return array
     */
    public function isJsonArrayReturnsBoolDataProvider()
    {
        return [
            [
                json_encode(['a']),
                true
            ],
            [
                json_encode('a,b:c'),
                false
            ],
            [
                json_encode(['object' => 'a']),
                true
            ],
            [
                json_encode([['title' => 'test2'], ['title' => 'test2']]),
                true
            ],
            [
                'a,b:c',
                false
            ],
            [
                [],
                false
            ]
        ];
    }

    /**
     * @param string $value
     * @param bool $expectedResult
     * @dataProvider isJsonArrayReturnsBoolDataProvider
     * @return void
     * @test
     * @covers ::isJsonArray
     */
    public function isJsonArrayReturnsBool($value, $expectedResult)
    {
        $this->assertSame($expectedResult, ArrayUtility::isJsonArray($value));
    }

    /**
     * Data Provider for htmlspecialcharsOnArrayReturnsArray()
     *
     * @return array
     */
    public function htmlspecialcharsOnArrayReturnsArrayDataProvider()
    {
        return [
            [
                [
                    '<te&st>'
                ],
                [
                    '&lt;te&amp;st&gt;'
                ]
            ],
            [
                [
                    '<test>',
                    [
                        '<test>' => '<test>'
                    ]
                ],
                [
                    '&lt;test&gt;',
                    [
                        '&lt;test&gt;' => '&lt;test&gt;'
                    ]
                ]
            ],
        ];
    }

    /**
     * @param array $array
     * @param array $expectedResult
     * @dataProvider htmlspecialcharsOnArrayReturnsArrayDataProvider
     * @return void
     * @test
     * @covers ::htmlspecialcharsOnArray
     */
    public function htmlspecialcharsOnArrayReturnsArray($array, $expectedResult)
    {
        $this->assertSame($expectedResult, ArrayUtility::htmlspecialcharsOnArray($array));
    }
}
