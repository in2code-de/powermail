<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class OrViewHelper
 */
class OrViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('array', 'array', 'Array with strings', true);
        $this->registerArgument('string', 'string', 'String to compare', false, '');
    }

    public function render(): bool
    {
        $string = $this->arguments['string'];
        foreach ($this->arguments['array'] as $value) {
            if (!empty($string) && $value) {
                return true;
            }

            if (!empty($string) && $value === $string) {
                return true;
            }
        }

        return false;
    }
}
