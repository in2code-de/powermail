<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class MorestepClassViewHelper
 */
class MorestepClassViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('activate', 'bool', 'Activate', true);
        $this->registerArgument('class', 'string', 'classname', false, 'powermail_morestep');
    }

    public function render(): string
    {
        if (!empty($this->arguments['activate'])) {
            return $this->arguments['class'];
        }

        return '';
    }
}
