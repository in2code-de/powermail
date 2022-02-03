<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Extbase\Object\Exception;
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    protected function getFlexFormArray(): ?array
    {
        $flexFormService = ObjectUtility::getObjectManager()->get(FlexFormService::class);
        return $flexFormService->convertFlexFormContentToArray($this->contentObject->data['pi_flexform']??'');
    }

    /**
     * Get TypoScript array
     *
     * @return array|null
     * @throws Exception
     */
    protected function getOverwriteTypoScript(): ?array
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
