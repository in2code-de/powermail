<?php
namespace In2code\Powermail\ViewHelpers\String;

use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper combines Raw and RemoveXss Methods
 *
 * @Todo: Should be renamed in a useful way in upcoming major version
 */
class RawAndRemoveXssViewHelper extends AbstractViewHelper
{

    /**
     * Disable escaping for TYPO3 7.6
     *
     * @var boolean
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * Disable escaping for TYPO3 8.x
     *
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * Disable escaping for TYPO3 8.x
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * ViewHelper combines Raw and RemoveXss Methods
     *
     * @return string
     */
    public function render()
    {
        $string = $this->renderChildren();
        if ($this->isHtmlEnabled()) {
            $string = GeneralUtility::removeXSS($string);
        } else {
            $string = htmlspecialchars($string);
        }
        return $string;
    }

    /**
     * @return bool
     */
    protected function isHtmlEnabled()
    {
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
        $settings = $configurationService->getTypoScriptSettings();
        return $settings['misc']['htmlForLabels'] === '1';
    }
}
