<?php

declare(strict_types=1);

namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * Class IsArrayViewHelper
 */
class IsArrayViewHelper extends AbstractViewHelper implements ViewHelperInterface
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('val', 'string', 'Value');
    }

    public function render(): bool
    {
        return is_array($this->arguments['val']);
    }
}
