<?php

namespace In2code\Powermail\Tests\Unit\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\ViewHelpers\Validation\FieldTypeFromValidationViewHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class FieldTypeFromValidationViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Validation\FieldTypeFromValidationViewHelper
 */
class FieldTypeFromValidationViewHelperTest extends UnitTestCase
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
            FieldTypeFromValidationViewHelper::class,
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
    public static function renderReturnsStringDataProvider(): array
    {
        return [
            'defaultWithHtml5' => [
                0,
                'text',
                true,
            ],
            'defaultWithoutHtml5' => [
                0,
                'text',
                false,
            ],
            'emailValidationWithoutHtml5' => [
                1,
                'text',
                false,
            ],
            'emailValidationWithHtml5' => [
                1,
                'email',
                true,
            ],
            'urlValidationWithoutHtml5' => [
                2,
                'text',
                false,
            ],
            'urlValidationWithHtml5' => [
                2,
                'url',
                true,
            ],
            'telValidationWithoutHtml5' => [
                3,
                'text',
                false,
            ],
            'telValidationWithHtml5' => [
                3,
                'tel',
                true,
            ],
            'numberValidationWithoutHtml5' => [
                4,
                'text',
                false,
            ],
            'numberValidationWithHtml5' => [
                4,
                'number',
                true,
            ],
            'rangeValidationWithoutHtml5' => [
                8,
                'text',
                false,
            ],
            'rangeValidationWithHtml5' => [
                8,
                'range',
                true,
            ],
        ];
    }

    /**
     * @param string $validation
     * @param string $expectedResult
     * @param bool $nativeValidationEnabled
     * @return void
     * @dataProvider renderReturnsStringDataProvider
     * @test
     * @covers ::render
     */
    public function renderReturnsString($validation, $expectedResult, $nativeValidationEnabled)
    {
        $this->abstractValidationViewHelperMock->_set(
            'settings',
            [
                'validation' => [
                    'native' => ($nativeValidationEnabled ? '1' : '0'),
                ],
            ]
        );
        $field = new Field();
        $field->setValidation($validation);

        $this->abstractValidationViewHelperMock->_set('arguments', ['field' => $field]);
        $result = $this->abstractValidationViewHelperMock->_call('render');
        self::assertSame($expectedResult, $result);
    }
}
