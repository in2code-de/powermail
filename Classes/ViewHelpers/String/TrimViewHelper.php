<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use In2code\Powermail\Utility\StringUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class TrimViewHelper
 */
class TrimViewHelper extends AbstractViewHelper
{
    /**
     * Trim outer and inner HTML for CSV files
     */
    public function render(): string
    {
        $string = $this->renderChildren();
        $string = $this->removeDuplicatedWhitespace($string);
        $string = StringUtility::br2nl($string);
        $string = $this->removeWhiteSpaceForEveryLine($string);
        return $this->removeCsvWhitespace($string);
    }

    /**
     * Replace duplicated whitespace with a single space
     */
    protected function removeDuplicatedWhitespace(string $string): string
    {
        return preg_replace('/\\s\\s+/', ' ', $string);
    }

    /**
     * Trim every single line
     */
    protected function removeWhiteSpaceForEveryLine(string $string): string
    {
        return preg_replace('/^\s+|\s+$/m', '', $string);
    }

    /**
     * Remove space in csv list (separated with semicolons)
     */
    protected function removeCsvWhitespace(string $string): string
    {
        return str_replace(['"; "', '" ; "', '" ;"'], '";"', $string);
    }
}
