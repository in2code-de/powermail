<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * Class SessionViewHelper
 */
class SessionViewHelper extends AbstractViewHelper
{

    /**
     * Session Key
     *
     * @var string
     */
    public $sessionKey = 'powermail_be_check_test';

    /**
     * Check if FE Sessions work in this instance
     *
     * @return bool
     */
    public function render()
    {
        $this->initializeTsfe();
        return $this->checkSession();
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function checkSession(): bool
    {
        $value = $this->getRandomValue();
        ObjectUtility::getTyposcriptFrontendController()->fe_user->setKey('ses', $this->sessionKey, $value);
        ObjectUtility::getTyposcriptFrontendController()->storeSessionData();
        return ObjectUtility::getTyposcriptFrontendController()->fe_user->getKey('ses', $this->sessionKey) === $value;
    }

    /**
     * @return string
     */
    protected function getRandomValue(): string
    {
        return md5((string)time());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function initializeTsfe()
    {
        $feUserAuthentication = EidUtility::initFeUser();
        ObjectUtility::getTyposcriptFrontendController() = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            ['FE' => ['disableNoCacheParameter' => 0]] + $GLOBALS['TYPO3_CONF_VARS'],
            GeneralUtility::_GET('id'),
            0,
            true
        );
        ObjectUtility::getTyposcriptFrontendController()->fe_user = $feUserAuthentication;
    }
}
