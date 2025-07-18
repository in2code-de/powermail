<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Getter;

use In2code\Powermail\Domain\Model\Field;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetPiVarAnswerFieldViewHelper
 */
class GetPiVarAnswerFieldViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('field', 'mixed', 'Field or Field UID', true);
        $this->registerArgument('piVars', 'array', 'Plugin variables', true);
    }

    public function render(): string
    {
        $result = '';
        $piVars = $this->arguments['piVars'];
        if (!empty($piVars['filter']['answer'][$this->getFieldUid()])) {
            return htmlspecialchars((string)$piVars['filter']['answer'][$this->getFieldUid()]);
        }

        return $result;
    }

    protected function getFieldUid(): int
    {
        $fieldUid = 0;
        $field = $this->arguments['field'];
        if (is_a($field, Field::class)) {
            /** @var Field $field */
            $fieldUid = $field->getUid();
        } elseif (is_numeric($field)) {
            $fieldUid = $field;
        }

        return $fieldUid;
    }
}
