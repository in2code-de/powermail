<?php

namespace In2code\Powermail\Tests\Unit\Domain\Service;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Service\GetNewMarkerNamesForFormService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class GetNewMarkerNamesForFormServiceTest
 * @coversDefaultClass \In2code\Powermail\Domain\Service\GetNewMarkerNamesForFormService
 */
class GetNewMarkerNamesForFormServiceTest extends UnitTestCase
{
    /**
     * @var GetNewMarkerNamesForFormService
     */
    protected $createMarkerMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->createMarkerMock = $this->getAccessibleMock(
            GetNewMarkerNamesForFormService::class,
            ['cleanString']
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->createMarkerMock);
    }

    public static function makeUniqueValueInArrayReturnsVoidDataProvider():array
    {
        return [
            [
                [
                    [
                        'marker' => 'abc',
                        'uid' => 12,
                        'title' => 'abc',
                    ],
                ],
                [
                    12 => 'abc',
                ],
                false,
            ],
            [
                [
                    [
                        'marker' => 'abc',
                        'uid' => 12,
                        'title' => 'abc',
                    ],
                    [
                        'marker' => 'abc',
                        'uid' => 13,
                        'title' => 'abc',
                    ],
                ],
                [
                    12 => 'abc',
                    13 => 'abc_01',
                ],
                false,
            ],
            [
                [
                    [
                        'marker' => 'abc',
                        'uid' => 12,
                        'title' => 'abc',
                    ],
                    [
                        'marker' => 'abc_01',
                        'uid' => 13,
                        'title' => 'abc',
                    ],
                    [
                        'marker' => 'abc_01',
                        'uid' => 14,
                        'title' => 'abc',
                    ],
                ],
                [
                    12 => 'abc',
                    13 => 'abc_01',
                    14 => 'abc_02',
                ],
                false,
            ],
            [
                [
                    [
                        'marker' => 'abc_01',
                        'uid' => 12,
                        'title' => 'abc',
                    ],
                    [
                        'marker' => 'abc_01',
                        'uid' => 13,
                        'title' => 'abc',
                    ],
                    [
                        'marker' => 'xxx',
                        'uid' => 14,
                        'title' => 'xxx',
                    ],
                ],
                [
                    12 => 'abc_01',
                    13 => 'abc_02',
                    14 => 'xxx',
                ],
                false,
            ],
            [
                [
                    [
                        'marker' => 'abc',
                        'uid' => 12,
                        'title' => 'def',
                    ],
                    [
                        'marker' => 'abc',
                        'uid' => 13,
                        'title' => 'def',
                    ],
                ],
                [
                    12 => 'abc',
                    13 => 'abc_01',
                ],
                false,
            ],
            [
                [
                    [
                        'marker' => 'abc',
                        'uid' => 12,
                        'title' => 'def',
                    ],
                    [
                        'marker' => 'abc',
                        'uid' => 13,
                        'title' => 'def',
                    ],
                ],
                [
                    12 => 'def',
                    13 => 'def_01',
                ],
                true,
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
        if ($forceReset) {
            $this->createMarkerMock->method('cleanString')->willReturnOnConsecutiveCalls('def', 'def_01');
        }
        self::assertSame(
            $expectedResult,
            $this->createMarkerMock->_call('makeUniqueValueInArray', $fieldArray, $forceReset)
        );
    }
}
