<?php
namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class TypoScriptUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\TypoScriptUtility
 */
class TypoScriptUtilityTest extends UnitTestCase
{

    protected $testFilesToDelete = [];

    /**
     *
     * @return void
     * @test
     * @covers ::getCaptchaExtensionFromSettings
     */
    public function getCaptchaExtensionFromSettingsReturnsString()
    {
        $settings = [
            'captcha' => [
                'use' => [
                    'captcha'
                ]
            ]
        ];
        $value = TypoScriptUtility::getCaptchaExtensionFromSettings($settings);
        $this->assertSame('default', $value);
    }
}
