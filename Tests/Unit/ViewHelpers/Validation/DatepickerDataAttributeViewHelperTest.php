<?php

namespace In2code\Powermail\Tests\Unit\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\ViewHelpers\Validation\DatepickerDataAttributeViewHelper;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

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
    public function setUp(): void
    {
        $this->abstractValidationViewHelperMock = $this->getAccessibleMock(
            DatepickerDataAttributeViewHelper::class,
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
     * Dataprovider for render()
     *
     * @return array
     */
    public static function renderReturnsArrayDataProvider(): array
    {
        return [
            'datepickerWithNativevalidationAndClientvalidationAndAdditionalAttributesAndMandatory' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '1',
                    ],
                ],
                [
                    'mandatory' => true,
                ],
                [
                    'data-company' => 'in2code',
                ],
                'anyvalue',
                [
                    'data-company' => 'in2code',
                    'data-datepicker-format' => 'YYYY-MM-DD HH:mm',
                    'data-date-value' => 'anyvalue',
                    'required' => 'required',
                    'aria-required' => 'true',
                    'data-powermail-required-message' => 'validationerror_mandatory',
                ],
            ],
            'datepickerWithNativevalidationAndClientvalidation' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '1',
                    ],
                ],
                [],
                [],
                'anyvalue',
                [
                    'data-datepicker-format' => 'YYYY-MM-DD HH:mm',
                    'data-date-value' => 'anyvalue',
                ],
            ],
            'datepickerWithNativevalidation' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '0',
                    ],
                ],
                [],
                [],
                '',
                [
                    'data-datepicker-format' => 'YYYY-MM-DD HH:mm',
                ],
            ],
            'datepickerWithClientvalidation' => [
                [
                    'validation' => [
                        'native' => '0',
                        'client' => '1',
                    ],
                ],
                [],
                [],
                '',
                [
                    'data-datepicker-format' => 'YYYY-MM-DD HH:mm',
                ],
            ],
            'datepickerWithoutValidation' => [
                [
                    'validation' => [
                        'native' => '0',
                        'client' => '0',
                    ],
                ],
                [],
                [],
                '',
                [
                    'data-datepicker-format' => 'YYYY-MM-DD HH:mm',
                ],
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
     * @throws InvalidExtensionNameException
     */
    public function renderReturnsArray($settings, $fieldProperties, $additionalAttributes, $value, $expectedResult)
    {
        $field = new Field();
        foreach ($fieldProperties as $propertyName => $propertyValue) {
            $field->_setProperty($propertyName, $propertyValue);
        }
        $this->abstractValidationViewHelperMock->_set('settings', $settings);
        $this->abstractValidationViewHelperMock->_set('extensionName', 'powermail');
        $this->abstractValidationViewHelperMock->_set('test', true);

        $arguments = [
            'field' => $field,
            'additionalAttributes' => $additionalAttributes,
            'value' => $value,
        ];
        $this->abstractValidationViewHelperMock->_set('arguments', $arguments);

        $result = $this->abstractValidationViewHelperMock->_call('render');
        self::assertSame($expectedResult, $result);
    }
}
