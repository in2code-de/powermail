<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsComposerModeViewHelper
 */
class IsComposerModeViewHelper extends AbstractViewHelper
{

    /**
     * @return bool
     */
    public function render(): bool
    {
        return defined('TYPO3_COMPOSER_MODE');
    }
}
