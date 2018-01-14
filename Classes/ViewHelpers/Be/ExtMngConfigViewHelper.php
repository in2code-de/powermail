<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Utility\ConfigurationUtility;
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
     */
    public function render(): bool
    {
        $configuration = ConfigurationUtility::getExtensionConfiguration();
        return is_array($configuration) && count($configuration) > 2;
    }
}
