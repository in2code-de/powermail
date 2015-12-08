<?php
namespace In2code\Powermail\ViewHelpers\String;

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
     * Trim Inner HTML
     *
     * @return bool
     */
    public function render()
    {
        // todo preg_match_all('/("[^"]+")/', $string, $result);
        $string = trim($this->renderChildren());
        $string = preg_replace('/\\s\\s+/', ' ', $string);
        $string = str_replace(['"; "', '" ; "', '" ;"'], '";"', $string);
        $string = str_replace(['<br />', '<br>', '<br/>'], PHP_EOL, $string);
        $string = str_replace([" \n ", "\n ", " \n"], PHP_EOL, $string);

        return $string;
    }
}
