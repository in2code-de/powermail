<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class EncodeViewHelper
 */
class EncodeViewHelper extends AbstractViewHelper
{

    /**
     * @return string
     */
    public function render()
    {
        return htmlspecialchars($this->renderChildren());
    }
}
