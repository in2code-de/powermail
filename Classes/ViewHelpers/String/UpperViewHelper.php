<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class UpperViewHelper
 */
class UpperViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('string', 'string', 'Any string', true);
    }

    public function render(): string
    {
        return ucfirst((string)$this->arguments['string']);
    }
}
