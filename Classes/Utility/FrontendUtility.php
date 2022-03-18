<?php
declare(strict_types = 1);
namespace In2code\Powermail\Utility;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Repository\UserRepository;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class FrontendUtility
 */
class FrontendUtility
{
    /**
     * Returns given number or the current PID
     *
     * @param int $pid Storage PID or nothing
     * @return int $pid
     */
    public static function getStoragePage(int $pid = 0): int
    {
        if ($pid === 0) {
            $pid = self::getCurrentPageIdentifier();
        }
        return (int)$pid;
    }

    /**
     * Get current page identifier
     *
     * @return int
     */
    public static function getCurrentPageIdentifier(): int
    {
        if (ObjectUtility::getTyposcriptFrontendController() !== null) {
            return (int)ObjectUtility::getTyposcriptFrontendController()->id;
        }
        return 0;
    }

    /**
     * Get configured frontend language
     *
     * @return int
     */
    public static function getSysLanguageUid(): int
    {
        $tsfe = ObjectUtility::getTyposcriptFrontendController();
        if ($tsfe !== null) {
            /** @var SiteLanguage $siteLanguage */
            $siteLanguage = $tsfe->getLanguage();
            return $siteLanguage->getLanguageId();
        }
        return 0;
    }

    /**
     * @return string
     */
    public static function getPluginName(): string
    {
        $pluginName = 'tx_powermail_pi1';
        if (self::isArgumentExisting('tx_powermail_pi2')) {
            $pluginName = 'tx_powermail_pi2';
        }
        if (self::isArgumentExisting('tx_powermail_web_powermailm1')) {
            $pluginName = 'tx_powermail_web_powermailm1';
        }
        return $pluginName;
    }

    /**
     * @return string
     */
    public static function getActionName(): string
    {
        $action = '';
        $arguments = self::getArguments(self::getPluginName());
        if (!empty($arguments['action'])) {
            $action = $arguments['action'];
        }
        return $action;
    }

    /**
     * Get charset for frontend rendering
     *
     * @return string
     */
    public static function getCharset(): string
    {
        return ObjectUtility::getTyposcriptFrontendController()->metaCharset;
    }

    /**
     * Check if logged in user is allowed to make changes in Pi2
     *
     * @param array $settings $settings TypoScript and Flexform Settings
     * @param int|Mail $mail
     * @return bool
     * @throws DBALException
     * @throws Exception
     * @codeCoverageIgnore
     */
    public static function isAllowedToEdit(array $settings, $mail): bool
    {
        if (!is_a($mail, Mail::class)) {
            $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
            $mail = $mailRepository->findByUid((int)$mail);
        }
        $feUser = ObjectUtility::getTyposcriptFrontendController()->fe_user->user['uid'] ?? 0;
        if ($feUser === 0 || $mail === null) {
            return false;
        }

        $feUserGroups = ObjectUtility::getTyposcriptFrontendController()->fe_user->user['usergroup'] ?? [];
        $usergroups = GeneralUtility::trimExplode(
            ',',
            $feUserGroups,
            true
        );
        $usersSettings = GeneralUtility::trimExplode(',', $settings['edit']['feuser'] ?? [], true);
        $usergroupsSettings = GeneralUtility::trimExplode(',', $settings['edit']['fegroup'] ?? [], true);

        // replace "_owner" with uid of owner in array with users
        if ($mail->getFeuser() !== null && is_numeric(array_search('_owner', $usersSettings))) {
            $usersSettings[array_search('_owner', $usersSettings)] = $mail->getFeuser()->getUid();
        }

        // add owner groups to allowed groups (if "_owner")
        if (is_numeric(array_search('_owner', $usergroupsSettings))) {
            /** @var UserRepository $userRepository */
            $userRepository = ObjectUtility::getObjectManager()->get(UserRepository::class);
            $usergroupsFromOwner = $userRepository->getUserGroupsFromUser($mail->getFeuser());
            $usergroupsSettings = array_merge((array)$usergroupsSettings, (array)$usergroupsFromOwner);
        }

        // 1. check user
        if (in_array($feUser, $usersSettings)) {
            return true;
        }

        // 2. check usergroup
        if (count(array_intersect($usergroups, $usergroupsSettings))) {
            return true;
        }

        return false;
    }

    /**
     * Is a frontend user logged in
     *
     * @return bool
     */
    public static function isLoggedInFrontendUser(): bool
    {
        return !empty(ObjectUtility::getTyposcriptFrontendController()->fe_user->user['uid']);
    }

    /**
     * Get Property from currently logged in fe_user
     *
     * @param string $propertyName
     * @return string
     */
    public static function getPropertyFromLoggedInFrontendUser(string $propertyName = 'uid'): string
    {
        $tsfe = ObjectUtility::getTyposcriptFrontendController();
        if (!empty($tsfe->fe_user->user[$propertyName])) {
            return (string)$tsfe->fe_user->user[$propertyName];
        }
        return '';
    }

    /**
     * Read domain from uri
     *
     * @param string $uri
     * @return string
     */
    public static function getDomainFromUri(string $uri): string
    {
        $uriParts = parse_url($uri);
        return (string)($uriParts['host'] ?? '');
    }

    /**
     * Get Country Name out of an IP address
     *
     * @param string $ipAddress
     * @return string
     */
    public static function getCountryFromIp(string $ipAddress = null): string
    {
        if ($ipAddress === null) {
            // @codeCoverageIgnoreStart
            $ipAddress = GeneralUtility::getIndpEnv('REMOTE_ADDR');
            // @codeCoverageIgnoreEnd
        }
        $country = '';
        $json = GeneralUtility::makeInstance(RequestFactory::class)
            ->request('http://ip-api.com/json/' . $ipAddress)
            ->getBody()
            ->getContents();
        if ($json) {
            $geoInfo = json_decode($json);
            if (!empty($geoInfo->country)) {
                $country = $geoInfo->country;
            }
        }
        return $country;
    }

    /**
     * Get Subfolder of current TYPO3 Installation
     *        and never return "//"
     *
     * @param bool $leadingSlash will be prepended
     * @param bool $trailingSlash will be appended
     * @param string $testHost can be used for a test
     * @param string $testUrl can be used for a test
     * @return string
     */
    public static function getSubFolderOfCurrentUrl(
        bool $leadingSlash = true,
        bool $trailingSlash = true,
        string $testHost = null,
        string $testUrl = null
    ) {
        $subfolder = '';
        $typo3RequestHost = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
        if ($testHost) {
            $typo3RequestHost = $testHost;
        }
        $typo3SiteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
        if ($testUrl) {
            $typo3SiteUrl = $testUrl;
        }

        // if subfolder
        if ($typo3RequestHost . '/' !== $typo3SiteUrl) {
            $subfolder = substr(str_replace($typo3RequestHost . '/', '', $typo3SiteUrl), 0, -1);
        }
        if ($trailingSlash && substr($subfolder, 0, -1) !== '/') {
            $subfolder .= '/';
        }
        if ($leadingSlash && $subfolder[0] !== '/') {
            $subfolder = '/' . $subfolder;
        }
        return $subfolder;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function isArgumentExisting(string $key): bool
    {
        return self::getArguments($key) !== [];
    }

    /**
     * Because GET params can be rewritten via routing configuration and so only available via TSFE on the one hand
     * and on the other hand some POST params are still available when a form is submitted, we need a function that
     * merges both sources
     *
     * @param string $key
     * @return array
     */
    public static function getArguments(string $key = 'tx_powermail_pi1'): array
    {
        return array_merge(
            self::getArgumentsFromTyposcriptFrontendController($key),
            self::getArgumentsFromGetOrPostRequest($key)
        );
    }

    /**
     * @param string $key
     * @return array
     */
    protected static function getArgumentsFromGetOrPostRequest(string $key): array
    {
        return (array)GeneralUtility::_GP($key);
    }

    /**
     * @param string $key
     * @return array
     */
    protected static function getArgumentsFromTyposcriptFrontendController(string $key): array
    {
        $typoScriptFrontend = ObjectUtility::getTyposcriptFrontendController();
        if ($typoScriptFrontend !== null) {
            /** @var PageArguments $pageArguments */
            $pageArguments = $typoScriptFrontend->getPageArguments();
            $arguments = $pageArguments->getArguments();
            if (array_key_exists($key, $arguments)) {
                return (array)$arguments[$key];
            }
        }
        return [];
    }
}
