<?php

declare(strict_types=1);

namespace In2code\Powermail\Tests\Unit\EventListener;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\EventListener\IgnorePreselectionForSubmittedFormEventListener;
use In2code\Powermail\Events\PrefillMultiFieldViewHelperEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(IgnorePreselectionForSubmittedFormEventListener::class)]
final class IgnorePreselectionForSubmittedFormEventListenerTest extends UnitTestCase
{
    /**
     * Options like in field settings "one|1", "two|2|*", "three|3"
     */
    protected const OPTIONS = [
        ['label' => 'one', 'value' => '1', 'selected' => 0],
        ['label' => 'two', 'value' => '2', 'selected' => 1],
        ['label' => 'three', 'value' => '3', 'selected' => 0],
    ];

    public function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        parent::tearDown();
    }

    public static function invokeDataProvider(): \Iterator
    {
        yield 'form not submitted: selection from view helper stays' => [
            [],
            [false, true, false],
            [false, true, false],
        ];
        yield 'another form submitted: selection from view helper stays' => [
            ['mail' => ['form' => '10'], 'field' => ['marker' => ['1']]],
            [true, true, false],
            [true, true, false],
        ];
        yield 'checkbox submitted with other option: preselection is removed' => [
            ['mail' => ['form' => '9'], 'field' => ['marker' => ['1']]],
            [true, true, false],
            [true, false, false],
        ];
        yield 'checkbox submitted with preselected and other option' => [
            ['mail' => ['form' => '9'], 'field' => ['marker' => ['1', '2']]],
            [true, true, false],
            [true, true, false],
        ];
        yield 'checkbox submitted by label' => [
            ['mail' => ['form' => '9'], 'field' => ['marker' => ['three']]],
            [false, true, true],
            [false, false, true],
        ];
        yield 'all checkboxes unchecked (back from confirmation): preselection is removed' => [
            ['mail' => ['form' => '9']],
            [false, true, false],
            [false, false, false],
        ];
        yield 'radio submitted with other option: preselection is removed' => [
            ['mail' => ['form' => '9'], 'field' => ['marker' => '3']],
            [false, true, true],
            [false, false, true],
        ];
        yield 'unselected options are never selected by the listener' => [
            ['mail' => ['form' => '9'], 'field' => ['marker' => ['1', '2', '3']]],
            [false, false, false],
            [false, false, false],
        ];
    }

    /**
     * @param array<string, mixed> $arguments submitted plugin arguments
     * @param list<bool> $selectedByViewHelper
     * @param list<bool> $expectedSelection
     */
    #[DataProvider('invokeDataProvider')]
    #[Test]
    public function invokeRemovesSelectionsThatWereNotSubmitted(
        array $arguments,
        array $selectedByViewHelper,
        array $expectedSelection
    ): void {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['powermail'] = ['replaceIrreWithElementBrowser' => '0'];
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://example.org/', 'POST'))
            ->withParsedBody(['tx_powermail_pi1' => $arguments])
            ->withAttribute('routing', new PageArguments(1, '0', []));

        $form = new Form();
        $form->_setProperty('uid', 9);
        $page = new Page();
        $page->setForm($form);
        $field = $this->createPartialMock(Field::class, ['getModifiedSettings']);
        $field->method('getModifiedSettings')->willReturn(self::OPTIONS);
        $field->setMarker('marker');
        $field->setPage($page);

        $listener = new IgnorePreselectionForSubmittedFormEventListener();
        $selection = [];
        foreach ($selectedByViewHelper as $index => $selected) {
            $event = new PrefillMultiFieldViewHelperEvent($selected, $field, null, $index + 1, false);
            $listener($event);
            $selection[] = $event->isSelected();
        }

        self::assertSame($expectedSelection, $selection);
    }
}
