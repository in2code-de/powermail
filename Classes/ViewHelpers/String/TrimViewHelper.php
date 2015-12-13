<?php
namespace In2code\Powermail\ViewHelpers\String;

use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Trim Inner HTML
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class TrimViewHelper extends AbstractViewHelper
{

    /**
     * Trim outer and inner HTML for CSV files
     *
     * @return string
     */
    public function render()
    {
        $string = trim($this->renderChildren());
        $string = $this->removeDuplicatedWhitespace($string);
        $string = $this->removeCsvWhitespace($string);
        $string = StringUtility::br2nl($string);
        return $string;
    }

    /**
     * Replace duplicated whitespace with a single space
     *
     * @param string $string
     * @return string
     */
    protected function removeDuplicatedWhitespace($string)
    {
        return preg_replace('/\\s\\s+/', ' ', $string);
    }

    /**
     * Remove space in csv list (separated with semicolons)
     *
     * @param string $string
     * @return string
     */
    protected function removeCsvWhitespace($string)
    {
        return str_replace(['"; "', '" ; "', '" ;"'], '";"', $string);
    }
}
