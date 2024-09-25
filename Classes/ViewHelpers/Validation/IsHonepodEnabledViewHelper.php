<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Domain\Validator\SpamShield\HoneyPodMethod;
use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsHonepodEnabledViewHelper
 */
class IsHonepodEnabledViewHelper extends AbstractViewHelper
{
    /**
     * @return bool
     */
    public function render(): bool
    {
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $settings = $configurationService->getTypoScriptSettings();
        return ConfigurationUtility::isValidationEnabled($settings, HoneyPodMethod::class);
    }
}
