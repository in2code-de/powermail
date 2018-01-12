<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ExtMngConfigViewHelper
 */
class ExtMngConfigViewHelper extends AbstractViewHelper
{

    /**
     * Check if Extension Manager Settings are available
     *
     * @return bool
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function render(): bool
    {
        $confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
        return is_array($confArr) && count($confArr) > 2;
    }
}
