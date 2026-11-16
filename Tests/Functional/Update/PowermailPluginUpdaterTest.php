<?php

declare(strict_types=1);

namespace In2code\Powermail\Tests\Functional\Update;

use In2code\Powermail\Update\PowermailPluginUpdater;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class PowermailPluginUpdaterTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'in2code/powermail',
    ];

    protected array $coreExtensionsToLoad = [
        'install',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/tt_content_null_flexform.csv');
    }

    #[Test]
    public function executeUpdateMigratesPluginsWithoutFlexForm(): void
    {
        $subject = new PowermailPluginUpdater();

        self::assertTrue($subject->updateNecessary());
        self::assertTrue($subject->executeUpdate());
        self::assertFalse($subject->updateNecessary());
        $this->assertCSVDataSet(__DIR__ . '/Fixtures/tt_content_null_flexform_migrated.csv');
    }
}
