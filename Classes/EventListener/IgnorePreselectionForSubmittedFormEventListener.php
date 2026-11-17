<?php

declare(strict_types=1);

namespace In2code\Powermail\EventListener;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Events\PrefillMultiFieldViewHelperEvent;
use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Core\Attribute\AsEventListener;

/**
 * If a form is rendered again (failed validation or back from confirmation view), only the submitted values decide
 * which checkboxes and radiobuttons are checked - not the preselection, TypoScript, session or frontend user.
 */
final class IgnorePreselectionForSubmittedFormEventListener
{
    #[AsEventListener(identifier: 'powermail/ignore-preselection-for-submitted-form')]
    public function __invoke(PrefillMultiFieldViewHelperEvent $event): void
    {
        $arguments = FrontendUtility::getArguments();
        $field = $event->getField();
        if ($event->isSelected() && $this->isFormOfFieldSubmitted($field, $arguments)) {
            $event->setSelected($this->isOptionSubmitted($field, $event->getCycle(), $arguments));
        }
    }

    /**
     * @param array<string, mixed> $arguments
     */
    protected function isFormOfFieldSubmitted(Field $field, array $arguments): bool
    {
        $formUid = $field->getPage()?->getForm()->getUid();
        return $formUid !== null && (int)($arguments['mail']['form'] ?? 0) === $formUid;
    }

    /**
     * Unchecked checkboxes and radiobuttons are not part of the submitted values
     *
     * @param array<string, mixed> $arguments
     */
    protected function isOptionSubmitted(Field $field, int $cycle, array $arguments): bool
    {
        $option = $field->getModifiedSettings()[$cycle - 1] ?? [];
        $submittedValues = (array)($arguments['field'][$field->getMarker()] ?? []);
        return in_array($option['value'] ?? null, $submittedValues, true)
            || in_array($option['label'] ?? null, $submittedValues, true);
    }
}
