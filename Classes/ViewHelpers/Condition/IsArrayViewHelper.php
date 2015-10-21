<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper check if given value is array or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsArrayViewHelper extends AbstractViewHelper
{

    /**
     * is_array()
     *
     * @param string|array $val
     * @return bool
     */
    public function render($val = null)
    {
        return is_array($val);
    }
}
