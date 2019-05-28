<?php
namespace In2code\Powermail\Tests\Unit\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\ViewHelpers\Validation\DatepickerDataAttributeViewHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Request;

/**
 * Class DatepickerDataAttributeViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Validation\DatepickerDataAttributeViewHelper
 */
class DatepickerDataAttributeViewHelperTest extends UnitTestCase
{

    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $abstractValidationViewHelperMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->abstractValidationViewHelperMock = $this->getAccessibleMock(
            DatepickerDataAttributeViewHelper::class,
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
     * Dataprovider for render()
     *
     * @return array
     */
    public function renderReturnsArrayDataProvider()
    {
        return [
            'datepickerWithNativevalidationAndClientvalidationAndAdditionalAttributesAndMandatory' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '1'
                    ]
                ],
                [
                    'mandatory' => 1
                ],
                [
                    'data-company' => 'in2code'
                ],
                'anyvalue',
                [
                    'data-company' => 'in2code',
                    'data-datepicker-force' => null,
                    'data-datepicker-settings' => 'date',
                    'data-datepicker-months' => 'datepicker_month_jan,datepicker_month_feb,datepicker_month_mar,' .
                        'datepicker_month_apr,datepicker_month_may,datepicker_month_jun,datepicker_month_jul,' .
                        'datepicker_month_aug,datepicker_month_sep,datepicker_month_oct,datepicker_month_nov,' .
                        'datepicker_month_dec',
                    'data-datepicker-days' => 'datepicker_day_so,datepicker_day_mo,datepicker_day_tu,' .
                        'datepicker_day_we,datepicker_day_th,datepicker_day_fr,datepicker_day_sa',
                    'data-datepicker-format' => 'Y-m-d H:i',
                    'data-date-value' => 'anyvalue',
                    'required' => 'required',
                    'aria-required' => 'true',
                    'data-parsley-required-message' => 'validationerror_mandatory',
                    'data-parsley-trigger' => 'change'
                ]
            ],
            'datepickerWithNativevalidationAndClientvalidation' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '1'
                    ]
                ],
                [],
                [],
                'anyvalue',
                [
                    'data-datepicker-force' => null,
                    'data-datepicker-settings' => 'date',
                    'data-datepicker-months' => 'datepicker_month_jan,datepicker_month_feb,datepicker_month_mar,' .
                        'datepicker_month_apr,datepicker_month_may,datepicker_month_jun,datepicker_month_jul,' .
                        'datepicker_month_aug,datepicker_month_sep,datepicker_month_oct,datepicker_month_nov,' .
                        'datepicker_month_dec',
                    'data-datepicker-days' => 'datepicker_day_so,datepicker_day_mo,datepicker_day_tu,' .
                        'datepicker_day_we,datepicker_day_th,datepicker_day_fr,datepicker_day_sa',
                    'data-datepicker-format' => 'Y-m-d H:i',
                    'data-date-value' => 'anyvalue',
                ]
            ],
            'datepickerWithNativevalidation' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '0'
                    ]
                ],
                [],
                [],
                '',
                [
                    'data-datepicker-force' => null,
                    'data-datepicker-settings' => 'date',
                    'data-datepicker-months' => 'datepicker_month_jan,datepicker_month_feb,datepicker_month_mar,' .
                        'datepicker_month_apr,datepicker_month_may,datepicker_month_jun,datepicker_month_jul,' .
                        'datepicker_month_aug,datepicker_month_sep,datepicker_month_oct,datepicker_month_nov,' .
                        'datepicker_month_dec',
                    'data-datepicker-days' => 'datepicker_day_so,datepicker_day_mo,datepicker_day_tu,' .
                        'datepicker_day_we,datepicker_day_th,datepicker_day_fr,datepicker_day_sa',
                    'data-datepicker-format' => 'Y-m-d H:i',
                ]
            ],
            'datepickerWithClientvalidation' => [
                [
                    'validation' => [
                        'native' => '0',
                        'client' => '1'
                    ]
                ],
                [],
                [],
                '',
                [
                    'data-datepicker-force' => null,
                    'data-datepicker-settings' => 'date',
                    'data-datepicker-months' => 'datepicker_month_jan,datepicker_month_feb,datepicker_month_mar,' .
                        'datepicker_month_apr,datepicker_month_may,datepicker_month_jun,datepicker_month_jul,' .
                        'datepicker_month_aug,datepicker_month_sep,datepicker_month_oct,datepicker_month_nov,' .
                        'datepicker_month_dec',
                    'data-datepicker-days' => 'datepicker_day_so,datepicker_day_mo,datepicker_day_tu,' .
                        'datepicker_day_we,datepicker_day_th,datepicker_day_fr,datepicker_day_sa',
                    'data-datepicker-format' => 'Y-m-d H:i',
                ]
            ],
            'datepickerWithoutValidation' => [
                [
                    'validation' => [
                        'native' => '0',
                        'client' => '0'
                    ]
                ],
                [],
                [],
                '',
                [
                    'data-datepicker-force' => null,
                    'data-datepicker-settings' => 'date',
                    'data-datepicker-months' => 'datepicker_month_jan,datepicker_month_feb,datepicker_month_mar,' .
                        'datepicker_month_apr,datepicker_month_may,datepicker_month_jun,datepicker_month_jul,' .
                        'datepicker_month_aug,datepicker_month_sep,datepicker_month_oct,datepicker_month_nov,' .
                        'datepicker_month_dec',
                    'data-datepicker-days' => 'datepicker_day_so,datepicker_day_mo,datepicker_day_tu,' .
                        'datepicker_day_we,datepicker_day_th,datepicker_day_fr,datepicker_day_sa',
                    'data-datepicker-format' => 'Y-m-d H:i',
                ]
            ],
        ];
    }

    /**
     * @param array $settings
     * @param array $fieldProperties
     * @param array $additionalAttributes
     * @param string $value
     * @param array $expectedResult
     * @return void
     * @dataProvider renderReturnsArrayDataProvider
     * @test
     * @covers ::render
     */
    public function renderReturnsArray($settings, $fieldProperties, $additionalAttributes, $value, $expectedResult)
    {
        $field = new Field;
        foreach ($fieldProperties as $propertyName => $propertyValue) {
            $field->_setProperty($propertyName, $propertyValue);
        }
        $this->abstractValidationViewHelperMock->_set('settings', $settings);
        $this->abstractValidationViewHelperMock->_set('extensionName', 'powermail');
        $this->abstractValidationViewHelperMock->_set('test', true);

        $controllerContext = new ControllerContext;
        $request = new Request;
        $request->setControllerExtensionName('powermail');
        $controllerContext->setRequest($request);
        $this->abstractValidationViewHelperMock->_set('controllerContext', $controllerContext);
        $arguments = [
            'field' => $field,
            'additionalAttributes' => $additionalAttributes,
            'value' => $value
        ];
        $this->abstractValidationViewHelperMock->_set('arguments', $arguments);

        $result = $this->abstractValidationViewHelperMock->_callRef('render');
        $this->assertSame($expectedResult, $result);
    }
}
