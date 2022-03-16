<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Be;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

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
    public function render(): bool
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
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->sessionKey, $value);
        return $GLOBALS['TSFE']->fe_user->getKey('ses', $this->sessionKey) === $value;
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
     * @throws Exception
     */
    protected function initializeTsfe(): void
    {
        $site = GeneralUtility::makeInstance(Site::class, 1, 1, []);
        $siteLanguage = GeneralUtility::makeInstance(
            SiteLanguage::class,
            0,
            'en-EN',
            new Uri('https://domain.org/page'),
            []
        );
        $pageArguments = GeneralUtility::makeInstance(PageArguments::class, 1, 0, []);
        $nullFrontend = GeneralUtility::makeInstance(NullFrontend::class, 'pages');
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $frontendUser = new FrontendUserAuthentication();
        try {
            $cacheManager->registerCache($nullFrontend);
        } catch (\Exception $exception) {
            unset($exception);
        }
        $GLOBALS['TSFE'] = new TypoScriptFrontendController(
            GeneralUtility::makeInstance(Context::class),
            $site,
            $siteLanguage,
            $pageArguments,
            $frontendUser
        );
        $GLOBALS['TSFE']->fe_user->initializeUserSessionManager();
        $GLOBALS['TSFE']->fe_user->createUserSession([]);
    }
}
