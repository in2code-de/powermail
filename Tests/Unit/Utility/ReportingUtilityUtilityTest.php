<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\ReportingUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class ReportingUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\ReportingUtility
 */
class ReportingUtilityTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Utility\ReportingUtility
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            ReportingUtility::class,
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
     * Data Provider for sortReportingArrayDescendingReturnsVoid()
     *
     * @return array
     */
    public function sortReportingArrayDescendingReturnsVoidDataProvider()
    {
        return [
            [
                [
                    [
                        'blue' => 5,
                        'black' => 1,
                        'red' => 2,
                        'yellow' => 9
                    ]
                ],
                [
                    [
                        'yellow' => 9,
                        'blue' => 5,
                        'red' => 2,
                        'black' => 1
                    ]
                ]
            ],
            [
                [
                    [
                        'a' => 5,
                        '' => 11,
                        '23' => 2,
                        'x ' => 9
                    ]
                ],
                [
                    [
                        '' => 11,
                        'x ' => 9,
                        'a' => 5,
                        '23' => 2
                    ]
                ]
            ],
        ];
    }

    /**
     * @param array $array
     * @param array $expectedResult
     * @return void
     * @dataProvider sortReportingArrayDescendingReturnsVoidDataProvider
     * @test
     * @covers ::sortReportingArrayDescending
     */
    public function sortReportingArrayDescendingReturnsVoid($array, $expectedResult)
    {
        ReportingUtility::sortReportingArrayDescending($array);
        $this->assertSame($array, $expectedResult);
    }

    /**
     * Data Provider for cutArrayByKeyLimitAndAddTotalValuesReturnsVoid()
     *
     * @return array
     */
    public function cutArrayByKeyLimitAndAddTotalValuesReturnsVoidDataProvider()
    {
        return [
            [
                [
                    [
                        'blue' => 5,
                        'black' => 1,
                        'red' => 2,
                        'yellow' => 9
                    ]
                ],
                [
                    [
                        'blue' => 5,
                        'black' => 1,
                        'others' => 11,
                    ]
                ]
            ],
            [
                [
                    [
                        'blue' => 2,
                        'black' => 3,
                        'red' => 4,
                        'yellow' => 5,
                        'brown' => 6,
                        'pink' => 7,
                        'orange' => 8,
                        'violet' => 9,
                        'green' => 3
                    ]
                ],
                [
                    [
                        'blue' => 2,
                        'black' => 3,
                        'others' => 42,
                    ]
                ]
            ],
        ];
    }

    /**
     * @param array $array
     * @param array $expectedResult
     * @return void
     * @dataProvider cutArrayByKeyLimitAndAddTotalValuesReturnsVoidDataProvider
     * @test
     * @covers ::cutArrayByKeyLimitAndAddTotalValues
     */
    public function cutArrayByKeyLimitAndAddTotalValuesReturnsVoid($array, $expectedResult)
    {
        ReportingUtility::cutArrayByKeyLimitAndAddTotalValues($array, 3, 'others');
        $this->assertSame($array, $expectedResult);
    }
}
