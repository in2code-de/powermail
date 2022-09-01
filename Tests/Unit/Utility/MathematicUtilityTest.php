<?php

namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Utility\MathematicUtility;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class MathematicUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\MathematicUtility
 */
class MathematicUtilityTest extends UnitTestCase
{
    /**
     * @var array
     */
    protected $testFilesToDelete = [];

    /**
     * Data Provider for mathematicOperationReturnsInt()
     *
     * @return array
     */
    public function mathematicOperationReturnsIntDataProvider()
    {
        return [
            [
                1,
                3,
                '+',
                4,
            ],
            [
                7,
                2,
                '-',
                5,
            ],
            [
                6,
                3,
                ':',
                2,
            ],
            [
                11,
                3,
                'x',
                33,
            ],
        ];
    }

    /**
     * @param int $number1
     * @param int $number2
     * @param string $operator
     * @param string $expectedResult
     * @dataProvider mathematicOperationReturnsIntDataProvider
     * @return void
     * @test
     * @covers ::mathematicOperation
     */
    public function mathematicOperationReturnsInt($number1, $number2, $operator, $expectedResult)
    {
        $result = MathematicUtility::mathematicOperation($number1, $number2, $operator);
        self::assertSame($expectedResult, $result);
    }
}
