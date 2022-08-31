<?php
namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\ReportingUtility;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;

/**
 * Class ReportingUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\ReportingUtility
 */
class ReportingUtilityTest extends UnitTestCase
{
    /**
     * @return void
     * @test
     * @covers ::getGroupedAnswersFromMails
     */
    public function getGroupedAnswersFromMailsReturnsArray()
    {
        $result = ReportingUtility::getGroupedAnswersFromMails($this->getDummyMails());
        $expected = [
            123 => [
                'abc' => 4
            ]
        ];
        $this->assertSame($expected, $result);
    }

    /**
     * @return void
     * @test
     * @covers ::getGroupedMarketingPropertiesFromMails
     * @throws PropertyNotAccessibleException
     */
    public function getGroupedMarketingPropertiesFromMailsReturnsArray()
    {
        $result = ReportingUtility::getGroupedMarketingPropertiesFromMails($this->getDummyMails());
        $expected = [
            'marketingRefererDomain' => [
                '-' => 4
            ],
            'marketingReferer' => [
                '-' => 4
            ],
            'marketingCountry' => [
                '-' => 4
            ],
            'marketingMobileDevice' => [
                '-' => 4
            ],
            'marketingFrontendLanguage' => [
                '-' => 4
            ],
            'marketingBrowserLanguage' => [
                '-' => 4
            ],
            'marketingPageFunnelString' => [
                '-' => 4
            ],
        ];
        $this->assertSame($expected, $result);
    }

    /**
     * @return Mail[]
     */
    protected function getDummyMails()
    {
        $mails = [];
        for ($i = 0; $i < 4; $i++) {
            $field = new Field();
            $field->_setProperty('uid', 123);
            $answer = new Answer();
            $answer->setField($field);
            $answer->setValue('abc');
            $mail = new Mail();
            $mail->addAnswer($answer);
            $mails[] = $mail;
        }
        return $mails;
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
