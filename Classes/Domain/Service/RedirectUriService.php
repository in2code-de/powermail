<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service;

use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class RedirectUriService
 */
class RedirectUriService
{
    /**
     * Get redirect URI from FlexForm or TypoScript
     */
    public function getRedirectUri(): ?string
    {
        $uri = null;
        $target = $this->getTarget();
        if ($target !== null) {
            return $this->contentObject->typoLink_URL(['parameter' => $target]);
        }

        return $uri;
    }

    /**
     * Get target
     */
    protected function getTarget(): ?string
    {
        $target = $this->getTargetFromTypoScript();
        if ($target === null) {
            return $this->getTargetFromFlexForm();
        }

        return $target;
    }

    /**
     * Get target from FlexForm
     *
     *      settings.flexform.thx.redirect
     */
    protected function getTargetFromFlexForm(): ?string
    {
        $target = null;
        $flexFormArray = $this->getFlexFormArray();
        if (!empty($flexFormArray['settings']['flexform']['thx']['redirect'])) {
            return $flexFormArray['settings']['flexform']['thx']['redirect'];
        }

        return $target;
    }

    /**
     * Get target from overwrite settings in TypoScript
     *
     *      plugin.tx_powermail.settings.setup.thx.overwrite.redirect = TEXT
     *      plugin.tx_powermail.settings.setup.thx.overwrite.redirect.value = 123
     */
    protected function getTargetFromTypoScript(): ?string
    {
        $target = null;
        $overwriteConfig = $this->getOverwriteTypoScript();
        if (!empty($overwriteConfig['redirect.'])) {
            return $this->contentObject->cObjGetSingle($overwriteConfig['redirect'], $overwriteConfig['redirect.']);
        }

        return $target;
    }

    /**
     * Get FlexForm array from contentObject
     */
    protected function getFlexFormArray(): ?array
    {
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        return $flexFormService->convertFlexFormContentToArray($this->contentObject->data['pi_flexform']??'');
    }

    /**
     * Get TypoScript array
     */
    protected function getOverwriteTypoScript(): ?array
    {
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $configuration = $configurationService->getTypoScriptConfiguration();
        if (!empty($configuration['thx.']['overwrite.'])) {
            return $configuration['thx.']['overwrite.'];
        }

        return null;
    }

    public function __construct(protected ContentObjectRenderer $contentObject)
    {
    }
}
