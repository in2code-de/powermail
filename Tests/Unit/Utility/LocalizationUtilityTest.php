<?php

namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class LocalizationUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\LocalizationUtility
 */
class LocalizationUtilityTest extends UnitTestCase
{
    /**
     * @test
     * @covers ::translate
     */
    public function translateReturnsString(): void
    {
        $value = (string)random_int(0, mt_getrandmax());
        self::assertSame($value, LocalizationUtility::translate($value));
        self::assertSame('Y-m-d H:i', LocalizationUtility::translate('datepicker_format'));
    }
}
