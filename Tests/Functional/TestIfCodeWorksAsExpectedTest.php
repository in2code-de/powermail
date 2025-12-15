<?php

declare(strict_types=1);

namespace In2code\Powermail\Tests\Functional;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\FormRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class TestIfCodeWorksAsExpectedTest extends FunctionalTestCase
{
    protected FormRepository $subject;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/powermail',
    ];

    protected array $coreExtensionsToLoad = ['core', 'extbase'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/tx_powermail_domain_model_form.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/tx_powermail_domain_model_page.csv');
    }

    /**
     * @test
     */
    public function testFunctionDoesTheThingWeWantTheWayWeWant(
    ): void {
        $subject = GeneralUtility::makeInstance(FormRepository::class);
        $result = $subject->findByPages(1);

        self::assertInstanceOf(Form::class, $result);
        // self::assertEquals(1, $result->getUid());
    }

    public function someDataProvider(): array
    {
        return [
            [1, 'otherData1', 'someResult1'],
            [2, 'otherData2', 'someResult2'],
            [2454, 'otherData2454', 'someResult2454'],
        ];
    }
}
