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
     * @return void
     * @test
     * @covers ::translate
     */
    public function translateReturnsString()
    {
        $value = (string)rand();
        self::assertSame($value, LocalizationUtility::translate($value));
        self::assertSame('Y-m-d H:i', LocalizationUtility::translate('datepicker_format'));
    }
}
