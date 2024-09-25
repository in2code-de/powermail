<?php

namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Utility\MathematicUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class MathematicUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\MathematicUtility
 */
class MathematicUtilityTest extends UnitTestCase
{
    /**
     * Data Provider for mathematicOperationReturnsInt()
     *
     * @return array
     */
    public static function mathematicOperationReturnsIntDataProvider(): array
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
