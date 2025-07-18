<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsStringInStringViewHelper
 */
class IsStringInStringViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('haystack', 'string', 'Haystack', true);
        $this->registerArgument('needle', 'string', 'Needle', true);
    }

    /**
     * Check if there is a string in another string
     */
    public function render(): bool
    {
        return stristr((string)$this->arguments['haystack'], (string)$this->arguments['needle']) !== false;
    }
}
