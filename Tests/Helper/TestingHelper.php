<?php

namespace In2code\Powermail\Tests\Helper;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectArrayContentObject;
use TYPO3\CMS\Frontend\ContentObject\TextContentObject;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class TestingHelper
 */
class TestingHelper
{
    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function setDefaultConstants()
    {
        $_SERVER['REMOTE_ADDR'] = '';
        $_SERVER['SSL_SESSION_ID'] = '';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'domain.org';
        $GLOBALS['TYPO3_CONF_VARS']['BE']['lockRootPath'] = '';
        $GLOBALS['TYPO3_CONF_VARS']['BE']['lockIP'] = 0;
        $GLOBALS['TYPO3_CONF_VARS']['BE']['lockIPv6'] = 0;
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['requestURIvar'] = null;
        $GLOBALS['TYPO3_CONF_VARS']['LOG'] = [];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['trustedHostsPattern'] = '.*';
        $GLOBALS['TYPO3_CONF_VARS']['BE']['fileCreateMask'] = '0775';
        $GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask'] = '2775';
        // @extensionScannerIgnoreLine
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['enable_DLOG'] = false;
        defined('TYPO3_OS') ?: define('TYPO3_OS', 'LINUX');
        defined('PATH_site') ?: define('PATH_site', self::getWebRoot());
        defined('PATH_thisScript') ?: define('PATH_thisScript', self::getWebRoot() . 'typo3');
        defined('TYPO3_version') ?: define('TYPO3_version', '10.2.2');
        defined('PHP_EXTENSIONS_DEFAULT') ?: define('PHP_EXTENSIONS_DEFAULT', 'php');
        defined('FILE_DENY_PATTERN_DEFAULT') ?: define('FILE_DENY_PATTERN_DEFAULT', '');
        try {
            SystemEnvironmentBuilder::run(0, SystemEnvironmentBuilder::REQUESTTYPE_CLI);
        } catch (\Exception $exception) {
            unset($exception);
        }
        defined('LF') ?: define('LF', chr(10));
        defined('CR') ?: define('CR', chr(13));
        defined('CRLF') ?: define('CRLF', CR . LF);
    }

    /**
     * @param int pid
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @throws Exception
     */
    public static function initializeTypoScriptFrontendController($pid = 1)
    {
        TestingHelper::setDefaultConstants();
        $configurationManager = new ConfigurationManager();
        $GLOBALS['TYPO3_CONF_VARS'] = $configurationManager->getDefaultConfiguration();
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['trustedHostsPattern'] = '.*';
        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = [
            'TEXT' => TextContentObject::class,
            'COA' => ContentObjectArrayContentObject::class,
        ];
        $GLOBALS['TT'] = new TimeTracker();
        $site = GeneralUtility::makeInstance(Site::class, $pid, 1, []);
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
        try {
            $cacheManager->registerCache($nullFrontend);
        } catch (\Exception $exception) {
            unset($exception);
        }
        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            GeneralUtility::makeInstance(Context::class),
            $site,
            $siteLanguage,
            $pageArguments
        );
        $GLOBALS['TSFE']->fe_user = new FrontendUserAuthentication();
        $GLOBALS['LANG'] = new LanguageService();
    }

    /**
     * @return string
     */
    public static function getWebRoot(): string
    {
        return realpath(__DIR__ . '/../../.Build/Web') . '/';
    }

    /**
     * @return ObjectManager
     */
    public static function getObjectManager()
    {
        return new ObjectManager();
    }
}
