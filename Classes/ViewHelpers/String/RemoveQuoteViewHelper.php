<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RemoveQuoteViewHelper
 */
class RemoveQuoteViewHelper extends AbstractViewHelper
{

    /**
     * @return string
     */
    public function render(): string
    {
        return str_replace('"', '\'', $this->renderChildren());
    }
}
