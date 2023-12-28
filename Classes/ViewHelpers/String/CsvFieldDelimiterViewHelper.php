<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CsvFieldDelimiterViewHelper
 *
 * ViewHelper adds field separator (;) and string delimiter (").
 * Replaces all double quotes (") in content and trims leading and trailing whitespace
 */
class CsvFieldDelimiterViewHelper extends AbstractViewHelper
{
    /**
     * @return string
     */
    public function render(): string
    {
        return '"' . str_replace('"', '\'', trim($this->renderChildren())) . '";';
    }
}
