<?php

namespace In2code\Powermail\Tests\Unit\ViewHelpers\Condition;

use In2code\Powermail\ViewHelpers\Condition\IsNotExcludedFromPowermailAllViewHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class IsNotExcludedFromPowermailAllViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Condition\IsNotExcludedFromPowermailAllViewHelper
 */
class IsNotExcludedFromPowermailAllViewHelperTest extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $isNotExcludedFromPowermailAllViewHelperMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->isNotExcludedFromPowermailAllViewHelperMock = $this->getAccessibleMock(
            IsNotExcludedFromPowermailAllViewHelper::class,
            null
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->isNotExcludedFromPowermailAllViewHelperMock);
    }

    /**
     * Dataprovider for getExcludedValuesReturnArray()
     *
     * @return array
     */
    public function getExcludedValuesReturnArrayDataProvider()
    {
        return [
            [
                'createAction',
                [
                    'excludeFromPowermailAllMarker' => [
                        'submitPage' => [
                            'excludeFromFieldTypes' => 'hidden, captcha, input',
                        ],
                    ],
                ],
                'excludeFromFieldTypes',
                [
                    'hidden',
                    'captcha',
                    'input',
                ],
            ],
            [
                'confirmationAction',
                [
                    'excludeFromPowermailAllMarker' => [
                        'confirmationPage' => [
                            'excludeFromFieldTypes' => 'hidden, input',
                        ],
                    ],
                ],
                'excludeFromFieldTypes',
                [
                    'hidden',
                    'input',
                ],
            ],
            [
                'sender',
                [
                    'excludeFromPowermailAllMarker' => [
                        'senderMail' => [
                            'excludeFromMarkerNames' => 'abc, daafsd',
                            'excludeFromFieldTypes' => 'hidden, captcha',
                        ],
                    ],
                ],
                'excludeFromFieldTypes',
                [
                    'hidden',
                    'captcha',
                ],
            ],
            [
                'receiver',
                [
                    'excludeFromPowermailAllMarker' => [
                        'receiverMail' => [
                            'excludeFromMarkerNames' => 'email, firstname',
                            'excludeFromFieldTypes' => 'hidden, input',
                        ],
                    ],
                ],
                'excludeFromMarkerNames',
                [
                    'email',
                    'firstname',
                ],
            ],
            [
                'optin',
                [
                    'excludeFromPowermailAllMarker' => [
                        'optinMail' => [
                            'excludeFromMarkerNames' => 'email, firstname',
                            'excludeFromFieldTypes' => 'hidden, input',
                        ],
                    ],
                ],
                'excludeFromMarkerNames',
                [
                    'email',
                    'firstname',
                ],
            ],
            [
                'optin',
                [
                    'excludeFromPowermailAllMarker' => [
                        'optinMail' => [
                            'excludeFromMarkerNames' => 'email, firstname',
                            'excludeFromFieldTypes' => 'hidden, input',
                        ],
                    ],
                ],
                'excludeFromFieldTypes',
                [
                    'hidden',
                    'input',
                ],
            ],
        ];
    }

    /**
     * @param string $type
     * @param array $settings
     * @param string $configurationType
     * @param array $expectedResult
     * @return void
     * @dataProvider getExcludedValuesReturnArrayDataProvider
     * @test
     * @covers ::render
     * @covers ::getExcludedValues
     */
    public function getExcludedValuesReturnArray($type, $settings, $configurationType, $expectedResult)
    {
        $result = $this->isNotExcludedFromPowermailAllViewHelperMock->_call(
            'getExcludedValues',
            $type,
            $settings,
            $configurationType
        );
        self::assertSame($expectedResult, $result);
    }
}
