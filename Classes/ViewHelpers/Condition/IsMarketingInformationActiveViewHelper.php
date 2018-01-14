<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsMarketingInformationActiveViewHelper
 */
class IsMarketingInformationActiveViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('marketingInformation', 'array', 'marketingInformation', true);
        $this->registerArgument('settings', 'array', 'TypoScript settings', true);
    }

    /**
     * Check if marketing information should be shown
     *
     * @return bool
     */
    public function render(): bool
    {
        $settings = $this->arguments['settings'];
        return (!empty($this->arguments['marketingInformation']) && !empty($settings['marketing']['information']) &&
            !ConfigurationUtility::isDisableMarketingInformationActive());
    }
}
