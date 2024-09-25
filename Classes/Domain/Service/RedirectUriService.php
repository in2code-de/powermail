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
     * @var ContentObjectRenderer
     */
    protected ContentObjectRenderer $contentObject;

    /**
     * Get redirect URI from FlexForm or TypoScript
     *
     * @return string|null
     */
    public function getRedirectUri(): ?string
    {
        $uri = null;
        $target = $this->getTarget();
        if ($target !== null) {
            $uri = $this->contentObject->typoLink_URL(['parameter' => $target]);
        }
        return $uri;
    }

    /**
     * Get target
     *
     * @return string|null
     */
    protected function getTarget(): ?string
    {
        $target = $this->getTargetFromTypoScript();
        if ($target === null) {
            $target = $this->getTargetFromFlexForm();
        }
        return $target;
    }

    /**
     * Get target from FlexForm
     *
     *      settings.flexform.thx.redirect
     *
     * @return string|null
     */
    protected function getTargetFromFlexForm(): ?string
    {
        $target = null;
        $flexFormArray = $this->getFlexFormArray();
        if (!empty($flexFormArray['settings']['flexform']['thx']['redirect'])) {
            $target = $flexFormArray['settings']['flexform']['thx']['redirect'];
        }
        return $target;
    }

    /**
     * Get target from overwrite settings in TypoScript
     *
     *      plugin.tx_powermail.settings.setup.thx.overwrite.redirect = TEXT
     *      plugin.tx_powermail.settings.setup.thx.overwrite.redirect.value = 123
     *
     * @return string|null
     */
    protected function getTargetFromTypoScript(): ?string
    {
        $target = null;
        $overwriteConfig = $this->getOverwriteTypoScript();
        if (!empty($overwriteConfig['redirect.'])) {
            $target = $this->contentObject->cObjGetSingle($overwriteConfig['redirect'], $overwriteConfig['redirect.']);
        }
        return $target;
    }

    /**
     * Get FlexForm array from contentObject
     *
     * @return array|null
     */
    protected function getFlexFormArray(): ?array
    {
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        return $flexFormService->convertFlexFormContentToArray($this->contentObject->data['pi_flexform']??'');
    }

    /**
     * Get TypoScript array
     *
     * @return array|null
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

    /**
     * @param ContentObjectRenderer $contentObject
     */
    public function __construct(ContentObjectRenderer $contentObject)
    {
        $this->contentObject = $contentObject;
    }
}
