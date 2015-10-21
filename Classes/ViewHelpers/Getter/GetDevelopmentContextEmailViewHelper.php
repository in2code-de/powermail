<?php
namespace In2code\Powermail\ViewHelpers\Getter;

use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetDevelopmentContextEmailViewHelper
 *
 * @package In2code\Powermail\ViewHelpers\Getter
 */
class GetDevelopmentContextEmailViewHelper extends AbstractViewHelper
{

    /**
     * Get developmentcontext email
     *
     * @return  false|string
     */
    public function render()
    {
        return ConfigurationUtility::getDevelopmentContextEmail();
    }
}
