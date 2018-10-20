<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
// @extensionScannerIgnoreLine Still needed for TYPO3 8.7
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class RedirectUriService
 */
class RedirectUriService
{

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * Get redirect URI from FlexForm or TypoScript
     *
     * @return string|null
     */
    public function getRedirectUri()
    {
        $uri = null;
        $target = $this->getTarget();
        if ($target !== null) {
            $uriBuilder = ObjectUtility::getObjectManager()->get(UriBuilder::class);
            $uriBuilder->setTargetPageUid($target);
            $uri = $uriBuilder->build();
        }
        return $uri;
    }

    /**
     * Get target
     *
     * @return string|null
     */
    protected function getTarget()
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
    protected function getTargetFromFlexForm()
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
    protected function getTargetFromTypoScript()
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
    protected function getFlexFormArray()
    {
        /** @var FlexFormService $flexFormService */
        $flexFormService = ObjectUtility::getObjectManager()->get(FlexFormService::class);
        return $flexFormService->convertFlexFormContentToArray($this->contentObject->data['pi_flexform']);
    }

    /**
     * Get TypoScript array
     *
     * @return array|null
     */
    protected function getOverwriteTypoScript()
    {
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
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
