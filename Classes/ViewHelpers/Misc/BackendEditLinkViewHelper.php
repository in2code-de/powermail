<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Utility\BackendUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * BackendEditLinkViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class BackendEditLinkViewHelper extends AbstractViewHelper
{

    /**
     * Create a link for backend edit
     *
     * @param string $tableName
     * @param int $identifier
     * @param bool $addReturnUrl
     * @return string
     */
    public function render($tableName, $identifier, $addReturnUrl = true)
    {
        return BackendUtility::createEditUri($tableName, $identifier, $addReturnUrl);
    }
}
