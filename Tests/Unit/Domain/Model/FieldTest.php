<?php

namespace In2code\Powermail\Tests\Unit\Domain\Model;

use In2code\Powermail\Tests\Unit\Fixtures\Domain\Model\FieldFixture;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class FieldTest
 * @coversDefaultClass \In2code\Powermail\Domain\Model\Field
 */
class FieldTest extends UnitTestCase
{
    /**
     * @var FieldFixture
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->generalValidatorMock = $this->getAccessibleMock(FieldFixture::class, null);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->generalValidatorMock);
    }

    public static function optionArrayReturnsArrayDataProvider(): array
    {
        return [
            [
                'abc',
                [
                    [
                        'label' => 'abc',
                        'value' => 'abc',
                        'selected' => 0,
                    ],
                ],
            ],
            [
                "red\nblue\nyellow",
                [
                    [
                        'label' => 'red',
                        'value' => 'red',
                        'selected' => 0,
                    ],
                    [
                        'label' => 'blue',
                        'value' => 'blue',
                        'selected' => 0,
                    ],
                    [
                        'label' => 'yellow',
                        'value' => 'yellow',
                        'selected' => 0,
                    ],
                ],
            ],
            [
                "please choose...|\nred\nblue|blue|*",
                [
                    [
                        'label' => 'please choose...',
                        'value' => '',
                        'selected' => 0,
                    ],
                    [
                        'label' => 'red',
                        'value' => 'red',
                        'selected' => 0,
                    ],
                    [
                        'label' => 'blue',
                        'value' => 'blue',
                        'selected' => 1,
                    ],
                ],
            ],
            [
                "||*\nred|red shoes",
                [
                    [
                        'label' => '',
                        'value' => '',
                        'selected' => 1,
                    ],
                    [
                        'label' => 'red',
                        'value' => 'red shoes',
                        'selected' => 0,
                    ],
                ],
            ],
            [
                "Red Shoes | 1 \nBlack Shoes | 2 | *\nBlue Shoes | ",
                [
                    [
                        'label' => 'Red Shoes',
                        'value' => '1',
                        'selected' => 0,
                    ],
                    [
                        'label' => 'Black Shoes',
                        'value' => '2',
                        'selected' => 1,
                    ],
                    [
                        'label' => 'Blue Shoes',
                        'value' => '',
                        'selected' => 0,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $value
     * @param array $expectedResult
     * @return void
     * @dataProvider optionArrayReturnsArrayDataProvider
     * @test
     * @covers ::optionArray
     */
    public function optionArrayReturnsArray($value, $expectedResult)
    {
        $result = $this->generalValidatorMock->_call('optionArray', $value, '', false);
        self::assertSame($expectedResult, $result);
    }

    public static function dataTypeFromFieldTypeReturnsStringDataProvider(): array
    {
        return [
            [
                'captcha',
                0,
            ],
            [
                'check',
                1,
            ],
            [
                'file',
                3,
            ],
            [
                'input',
                0,
            ],
            [
                'textarea',
                0,
            ],
            [
                'select',
                0,
            ],
            [
                'select',
                1,
                true,
            ],
        ];
    }

    /**
     * @param string $fieldType
     * @param array $expectedResult
     * @param bool $multiple
     * @return void
     * @dataProvider dataTypeFromFieldTypeReturnsStringDataProvider
     * @test
     * @covers ::dataTypeFromFieldType
     */
    public function dataTypeFromFieldTypeReturnsString($fieldType, $expectedResult, $multiple = false)
    {
        if ($multiple) {
            $this->generalValidatorMock->_set('multiselect', $multiple);
        }
        $result = $this->generalValidatorMock->_call('dataTypeFromFieldType', $fieldType);
        self::assertSame($expectedResult, $result);
    }
}
