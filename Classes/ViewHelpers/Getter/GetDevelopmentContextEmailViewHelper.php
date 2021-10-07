<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Getter;

use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetDevelopmentContextEmailViewHelper
 */
class GetDevelopmentContextEmailViewHelper extends AbstractViewHelper
{

    /**
     * @return string
     */
    public function render(): string
    {
        return ConfigurationUtility::getDevelopmentContextEmail();
    }
}
