<?php

namespace In2code\Powermail\Tests\ViewHelpers\Reporting;

use In2code\Powermail\ViewHelpers\Reporting\GetLabelsForChartsViewHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class GetLabelsForChartsViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Reporting\GetLabelsForChartsViewHelper
 */
class GetLabelsForChartsViewHelperTest extends UnitTestCase
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
            GetLabelsForChartsViewHelper::class,
            null
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
    public static function renderReturnsStringDataProvider(): array
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
                15,
                '...',
                false,
                'label1,label2,label3',
            ],
            [
                [
                    'a' => [
                        'abc' => '10',
                        'def' => '70',
                        'gh' => '20',
                    ],
                ],
                'a',
                ',',
                2,
                '...',
                true,
                'ab...%2Cde...%2Cgh',
            ],
            [
                [
                    '0' => [
                        'typo3' => '15',
                        'wordpress' => '60',
                        'drupal' => '25',
                    ],
                ],
                '0',
                '|',
                5,
                ' etc...',
                false,
                'typo3|wordp etc...|drupa etc...',
            ],
            [
                [
                    '0' => [
                        '"Fußgänger"' => '15',
                        '"Auto, LKW, Krafträder"' => '60',
                        'Fahrradfahrer' => '25',
                    ],
                ],
                '0',
                ',',
                15,
                '...',
                false,
                'Fußgänger,Auto LKW Kraftr...,Fahrradfahrer',
            ],
        ];
    }

    /**
     * @param array $answers Array with answeres
     * @param string $field Fieldname (key of answers array)
     * @param string $separator
     * @param int $crop
     * @param string $append
     * @param bool $urlEncode
     * @return void
     * @dataProvider renderReturnsStringDataProvider
     * @test
     * @covers ::render
     */
    public function renderReturnsString($answers, $field, $separator, $crop, $append, $urlEncode, $expectedResult)
    {
        $arguments = [
            'answers' => $answers,
            'fieldUidOrKey' => $field,
            'separator' => $separator,
            'crop' => $crop,
            'append' => $append,
            'urlEncode' => $urlEncode,
        ];
        $this->abstractValidationViewHelperMock->_set('arguments', $arguments);
        $result = $this->abstractValidationViewHelperMock->_call('render');
        self::assertSame($expectedResult, $result);
    }
}
