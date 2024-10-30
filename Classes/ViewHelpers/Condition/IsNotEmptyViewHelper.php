<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Utility\StringUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsNotEmptyViewHelper
 */
class IsNotEmptyViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('val', 'string', 'Value', true);
    }

    public function render(): bool
    {
        return StringUtility::isNotEmpty($this->arguments['val']);
    }
}
