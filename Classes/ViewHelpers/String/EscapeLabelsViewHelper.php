<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use In2code\Powermail\Domain\Service\ConfigurationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     */
    protected function isHtmlEnabled(): bool
    {
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $settings = $configurationService->getTypoScriptSettings();
        return ($settings['misc']['htmlForLabels'] ?? '') === '1';
    }
}
