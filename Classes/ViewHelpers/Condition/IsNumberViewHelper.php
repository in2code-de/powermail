<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * View helper check if given value is number or not
 */
class IsNumberViewHelper extends AbstractViewHelper implements ViewHelperInterface
{
    /**
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('val', 'string', 'Value');
    }

    public function render(): bool
    {
        return is_numeric($this->arguments['val']);
    }
}
