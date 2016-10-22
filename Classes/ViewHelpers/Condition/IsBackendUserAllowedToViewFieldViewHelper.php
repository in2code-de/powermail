<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Utility\BackendUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsBackendUserAllowedToViewFieldViewHelper
 */
class IsBackendUserAllowedToViewFieldViewHelper extends AbstractViewHelper
{

    /**
     * Check if Backend User is allowed to see this field
     *
     * @param string $table
     * @param string $field
     * @return bool
     */
    public function render($table, $field)
    {
        return BackendUtility::getBackendUserAuthentication()->check('non_exclude_fields', $table . ':' . $field);
    }
}
