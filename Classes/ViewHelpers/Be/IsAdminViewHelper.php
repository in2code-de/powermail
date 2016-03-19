<?php
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Utility\BackendUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Is Backend Admin?
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsAdminViewHelper extends AbstractViewHelper
{

    /**
     * Is Backend Admin?
     *
     * @return bool
     */
    public function render()
    {
        return BackendUtility::isBackendAdmin();
    }
}
