<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsMarketingInformationEnabledGloballyViewHelper
 *
 * @package In2code\Powermail\ViewHelpers\Condition
 */
class IsMarketingInformationEnabledGloballyViewHelper extends AbstractViewHelper
{

    /**
     * Check if marketing information should be shown
     *
     * @return bool
     */
    public function render()
    {
        return !ConfigurationUtility::isDisableMarketingInformationActive();
    }
}
