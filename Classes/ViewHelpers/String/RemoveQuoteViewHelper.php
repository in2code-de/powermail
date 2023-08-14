<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use In2code\Powermail\Exception\DeprecatedException;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RemoveQuoteViewHelper
 */
class RemoveQuoteViewHelper extends AbstractViewHelper
{
    /**
     * @return string
     * @throws DeprecatedException
     */
    public function render(): string
    {
        return str_replace('"', '\'', $this->renderChildren());
    }
}
