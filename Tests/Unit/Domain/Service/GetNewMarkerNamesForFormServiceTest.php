<?php
namespace In2code\Powermail\Tests\Unit\Domain\Service;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Service\GetNewMarkerNamesForFormService;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class GetNewMarkerNamesForFormServiceTest
 * @coversDefaultClass \In2code\Powermail\Domain\Service\GetNewMarkerNamesForFormService
 */
class GetNewMarkerNamesForFormServiceTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Service\GetNewMarkerNamesForFormService
     */
    protected $createMarkerMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->createMarkerMock = $this->getAccessibleMock(
            GetNewMarkerNamesForFormService::class,
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->createMarkerMock);
    }

    /**
     * Dataprovider cleanStringReturnsString()
     *
     * @return array
     */
    public function cleanStringReturnsStringDataProvider()
    {
        return [
            [
                'test',
                'default',
                'test',
            ],
            [
                'This is A Test',
                'default',
                'thisisatest',
            ],
            [
                '$T h%is_-',
                'default',
                'this__',
            ],
            [
                'ęąśółżźćäöüśćóß',
                'default',
                'easolzzcaeoeuescoss',
            ]
        ];
    }

    /**
     * @param string $string
     * @param string $defaultValue
     * @param string $expectedResult
     * @return void
     * @dataProvider cleanStringReturnsStringDataProvider
     * @test
     * @covers ::cleanString
     */
    public function cleanStringReturnsString($string, $defaultValue, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->createMarkerMock->_callRef('cleanString', $string, $defaultValue));
    }

    /**
     * Dataprovider makeUniqueValueInArrayReturnsVoid()
     *
     * @return array
     */
    public function makeUniqueValueInArrayReturnsVoidDataProvider()
    {
        return [
            [
                [
                    [
                        'marker' => 'abc',
                        'uid' => 12,
                        'title' => 'abc'
                    ]
                ],
                [
                    12 => 'abc'
                ],
                false
            ],
            [
                [
                    [
                        'marker' => 'abc',
                        'uid' => 12,
                        'title' => 'abc'
                    ],
                    [
                        'marker' => 'abc',
                        'uid' => 13,
                        'title' => 'abc'
                    ]
                ],
                [
                    12 => 'abc',
                    13 => 'abc_01'
                ],
                false
            ],
            [
                [
                    [
                        'marker' => 'abc',
                        'uid' => 12,
                        'title' => 'abc'
                    ],
                    [
                        'marker' => 'abc_01',
                        'uid' => 13,
                        'title' => 'abc'
                    ],
                    [
                        'marker' => 'abc_01',
                        'uid' => 14,
                        'title' => 'abc'
                    ]
                ],
                [
                    12 => 'abc',
                    13 => 'abc_01',
                    14 => 'abc_02',
                ],
                false
            ],
            [
                [
                    [
                        'marker' => 'abc_01',
                        'uid' => 12,
                        'title' => 'abc'
                    ],
                    [
                        'marker' => 'abc_01',
                        'uid' => 13,
                        'title' => 'abc'
                    ],
                    [
                        'marker' => 'xxx',
                        'uid' => 14,
                        'title' => 'xxx'
                    ]
                ],
                [
                    12 => 'abc_01',
                    13 => 'abc_02',
                    14 => 'xxx',
                ],
                false
            ],
            [
                [
                    [
                        'marker' => 'abc',
                        'uid' => 12,
                        'title' => 'def'
                    ],
                    [
                        'marker' => 'abc',
                        'uid' => 13,
                        'title' => 'def'
                    ]
                ],
                [
                    12 => 'abc',
                    13 => 'abc_01',
                ],
                false
            ],
            [
                [
                    [
                        'marker' => 'abc',
                        'uid' => 12,
                        'title' => 'def'
                    ],
                    [
                        'marker' => 'abc',
                        'uid' => 13,
                        'title' => 'def'
                    ]
                ],
                [
                    12 => 'def',
                    13 => 'def_01',
                ],
                true
            ],
        ];
    }

    /**
     * @param array $propertiesFields
     * @param array $expectedResult
     * @return void
     * @dataProvider makeUniqueValueInArrayReturnsVoidDataProvider
     * @test
     * @covers ::makeUniqueValueInArray
     */
    public function makeUniqueValueInArrayReturnsVoid($propertiesFields, $expectedResult, $forceReset)
    {
        $fieldArray = [];
        foreach ($propertiesFields as $properties) {
            $field = new Field();
            foreach ($properties as $key => $value) {
                $field->_setProperty($key, $value);
            }
            $fieldArray[$field->getUid()] = $field;
        }
        $this->assertSame(
            $expectedResult,
            $this->createMarkerMock->_callRef('makeUniqueValueInArray', $fieldArray, $forceReset)
        );
    }
}
