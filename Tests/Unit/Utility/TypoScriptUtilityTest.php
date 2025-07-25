<?php

namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class TypoScriptUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\TypoScriptUtility
 */
class TypoScriptUtilityTest extends UnitTestCase
{
    /**
     * @test
     * @covers ::getCaptchaExtensionFromSettings
     */
    public function getCaptchaExtensionFromSettingsReturnsString(): void
    {
        $settings = [
            'captcha' => [
                'use' => [
                    'captcha',
                ],
            ],
        ];
        $value = TypoScriptUtility::getCaptchaExtensionFromSettings($settings);
        self::assertSame('default', $value);
    }
}
