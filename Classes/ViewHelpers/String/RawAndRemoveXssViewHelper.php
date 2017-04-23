<?php
namespace In2code\Powermail\ViewHelpers\String;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Service\TypoScriptService;
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
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

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
        $settings = $this->getSettings();
        return $settings['misc']['htmlForLabels'] === '1';
    }

    /**
     * @return array
     */
    protected function getSettings()
    {
        $typoScriptSetup = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
        $configuration = $typoScriptService->convertTypoScriptArrayToPlainArray($typoScriptSetup);
        return (array)$configuration['plugin']['tx_powermail']['settings']['setup'];
    }
}
