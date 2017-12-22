<?php
namespace In2code\Powermail\Tests\Domain\Model;

use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class FieldTest
 * @coversDefaultClass \In2code\Powermail\Domain\Model\Field
 */
class FieldTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Tests\Fixtures\Domain\Model\FieldFixture
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        require_once(dirname(dirname(dirname(__FILE__))) . '/Fixtures/Domain/Model/FieldFixture.php');
        $this->generalValidatorMock = $this->getAccessibleMock(
            '\In2code\Powermail\Tests\Fixtures\Domain\Model\FieldFixture',
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
     * Dataprovider optionArrayReturnsArray()
     *
     * @return array
     */
    public function optionArrayReturnsArrayDataProvider()
    {
        return [
            [
                'abc',
                [
                    [
                        'label' => 'abc',
                        'value' => 'abc',
                        'selected' => 0
                    ],
                ]
            ],
            [
                "red\nblue\nyellow",
                [
                    [
                        'label' => 'red',
                        'value' => 'red',
                        'selected' => 0
                    ],
                    [
                        'label' => 'blue',
                        'value' => 'blue',
                        'selected' => 0
                    ],
                    [
                        'label' => 'yellow',
                        'value' => 'yellow',
                        'selected' => 0
                    ],
                ]
            ],
            [
                "please choose...|\nred\nblue|blue|*",
                [
                    [
                        'label' => 'please choose...',
                        'value' => '',
                        'selected' => 0
                    ],
                    [
                        'label' => 'red',
                        'value' => 'red',
                        'selected' => 0
                    ],
                    [
                        'label' => 'blue',
                        'value' => 'blue',
                        'selected' => 1
                    ],
                ]
            ],
            [
                "||*\nred|red shoes",
                [
                    [
                        'label' => '',
                        'value' => '',
                        'selected' => 1
                    ],
                    [
                        'label' => 'red',
                        'value' => 'red shoes',
                        'selected' => 0
                    ],
                ]
            ],
            [
                "Red Shoes | 1 \nBlack Shoes | 2 | *\nBlue Shoes | ",
                [
                    [
                        'label' => 'Red Shoes',
                        'value' => '1',
                        'selected' => 0
                    ],
                    [
                        'label' => 'Black Shoes',
                        'value' => '2',
                        'selected' => 1
                    ],
                    [
                        'label' => 'Blue Shoes',
                        'value' => '',
                        'selected' => 0
                    ],
                ]
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
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider dataTypeFromFieldTypeReturnsString()
     *
     * @return array
     */
    public function dataTypeFromFieldTypeReturnsStringDataProvider()
    {
        return [
            [
                'captcha',
                0
            ],
            [
                'check',
                1
            ],
            [
                'file',
                3
            ],
            [
                'input',
                0
            ],
            [
                'textarea',
                0
            ],
            [
                'select',
                0
            ],
            [
                'select',
                1,
                true
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
        $this->assertSame($expectedResult, $result);
    }
}
