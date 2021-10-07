<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsDevelopmentContextViewHelper
 */
class IsDevelopmentContextViewHelper extends AbstractViewHelper
{

    /**
     * @return bool
     */
    public function render(): bool
    {
        return Environment::getContext()->isDevelopment();
    }
}
