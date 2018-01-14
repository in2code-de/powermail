<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Utility\BackendUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsAdminViewHelper
 */
class IsAdminViewHelper extends AbstractViewHelper
{

    /**
     * Is Backend Admin?
     *
     * @return bool
     */
    public function render(): bool
    {
        return BackendUtility::isBackendAdmin();
    }
}
