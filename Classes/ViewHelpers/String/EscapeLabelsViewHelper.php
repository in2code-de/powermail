<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\String;

use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class EscapeLabelsViewHelper
 */
class EscapeLabelsViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Decide if a string should be escaped or not depending on
     *      settings.misc.htmlForLabels=1
     *
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        $string = $this->renderChildren();
        if ($this->isHtmlEnabled() === false) {
            $string = htmlspecialchars($string);
        }
        return $string;
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function isHtmlEnabled(): bool
    {
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
        $settings = $configurationService->getTypoScriptSettings();
        return $settings['misc']['htmlForLabels'] === '1';
    }
}
