<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Service to get the redirect URI
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class RedirectUriService
{

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
     * @inject
     */
    protected $uriBuilder;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

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
            $this->uriBuilder->setTargetPageUid($target);
            $uri = $this->uriBuilder->build();
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
        $typoScript = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        if (!empty($typoScript['plugin.']['tx_powermail.']['settings.']['setup.']['thx.']['overwrite.'])) {
            return $typoScript['plugin.']['tx_powermail.']['settings.']['setup.']['thx.']['overwrite.'];
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
