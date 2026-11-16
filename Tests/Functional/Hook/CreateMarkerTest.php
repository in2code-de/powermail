<?php

declare(strict_types=1);

namespace In2code\Powermail\Tests\Functional\Hook;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Hook\CreateMarker;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class CreateMarkerTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'in2code/powermail',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/form_with_null_text_columns.csv');
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        parent::tearDown();
    }

    #[Test]
    public function savingFormWithExistingFieldContainingNullTextColumnsDoesNotFail(): void
    {
        $properties = ['title' => 'Contact'];
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://example.org/typo3/record/edit', 'POST'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withParsedBody(['data' => [Form::TABLE_NAME => [1 => $properties]]]);

        (new CreateMarker())->processDatamap_postProcessFieldArray('update', Form::TABLE_NAME, '1', $properties);

        self::assertSame(['title' => 'Contact'], $properties);
    }
}
