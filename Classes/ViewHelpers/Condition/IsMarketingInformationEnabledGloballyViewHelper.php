<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsMarketingInformationEnabledGloballyViewHelper
 */
class IsMarketingInformationEnabledGloballyViewHelper extends AbstractViewHelper
{

    /**
     * @return bool
     */
    public function render(): bool
    {
        return !ConfigurationUtility::isDisableMarketingInformationActive();
    }
}
