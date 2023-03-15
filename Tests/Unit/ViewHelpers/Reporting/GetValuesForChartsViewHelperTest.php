<?php

namespace In2code\Powermail\Tests\Unit\ViewHelpers\Reporting;

use In2code\Powermail\ViewHelpers\Reporting\GetValuesForChartsViewHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class GetValuesForChartsViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Reporting\GetValuesForChartsViewHelper
 */
class GetValuesForChartsViewHelperTest extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $abstractValidationViewHelperMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->abstractValidationViewHelperMock = $this->getAccessibleMock(
            GetValuesForChartsViewHelper::class,
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->generalValidatorMock);
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
                [
                    'test' => [
                        'label1' => '10',
                        'label2' => '70',
                        'label3' => '20',
                    ],
                ],
                'test',
                ',',
                false,
                '10,70,20',
            ],
            [
                [
                    'a' => [
                        'label1' => '12',
                        'label2' => '70',
                        'label3' => '18',
                    ],
                ],
                'a',
                ',',
                true,
                '12%2C70%2C18',
            ],
            [
                [
                    'a' => [
                        'label1' => '"1|2"',
                        'label2' => '70|',
                        'label3' => '|18',
                    ],
                ],
                'a',
                '|',
                false,
                '12|70|18',
            ],
        ];
    }

    /**
     * @param array $answers Array with answeres
     * @param string $field Fieldname (key of answers array)
     * @param string $glue
     * @param bool $urlEncode
     * @param string $expectedResult
     * @return void
     * @dataProvider renderReturnsStringDataProvider
     * @test
     * @covers ::render
     */
    public function renderReturnsString($answers, $field, $glue, $urlEncode, $expectedResult)
    {
        $arguments = [
            'answers' => $answers,
            'fieldUidOrKey' => $field,
            'separator' => $glue,
            'urlEncode' => $urlEncode,
        ];
        $this->abstractValidationViewHelperMock->_set('arguments', $arguments);
        $result = $this->abstractValidationViewHelperMock->_call('render', $answers, $field, $glue, $urlEncode);
        self::assertSame($expectedResult, $result);
    }
}
