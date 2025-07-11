<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class EncodeViewHelper
 */
class EncodeViewHelper extends AbstractViewHelper
{
    public function render(): string
    {
        return htmlspecialchars((string)$this->renderChildren());
    }
}
