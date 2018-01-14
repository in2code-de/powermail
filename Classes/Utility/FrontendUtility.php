<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Repository\UserRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FrontendUtility
 */
class FrontendUtility extends AbstractUtility
{

    /**
     * Returns given number or the current PID
     *
     * @param integer $pid Storage PID or nothing
     * @return integer $pid
     */
    public static function getStoragePage($pid = 0)
    {
        if (!$pid) {
            $pid = self::getCurrentPageIdentifier();
        }
        return (int)$pid;
    }

    /**
     * Get current page identifier
     *
     * @return int
     */
    public static function getCurrentPageIdentifier()
    {
        return (int)self::getTyposcriptFrontendController()->id;
    }

    /**
     * Get configured frontend language
     *
     * @return int
     */
    public static function getSysLanguageUid()
    {
        return (int)self::getTyposcriptFrontendController()->tmpl->setup['config.']['sys_language_uid'];
    }

    /**
     * @return string
     */
    public static function getPluginName(): string
    {
        $pluginName = 'tx_powermail_pi1';
        if (!empty(GeneralUtility::_GPmerged('tx_powermail_pi2'))) {
            $pluginName = 'tx_powermail_pi2';
        }
        if (!empty(GeneralUtility::_GPmerged('tx_powermail_web_powermailm1'))) {
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
        $plugin = self::getPluginName();
        $arguments = GeneralUtility::_GPmerged($plugin);
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
    public static function getCharset()
    {
        return self::getTyposcriptFrontendController()->metaCharset;
    }

    /**
     * Check if logged in user is allowed to make changes in Pi2
     *
     * @param array $settings $settings TypoScript and Flexform Settings
     * @param int|Mail $mail
     * @return bool
     * @codeCoverageIgnore
     */
    public static function isAllowedToEdit($settings, $mail)
    {
        if (!is_a($mail, Mail::class)) {
            /** @var MailRepository $mailRepository */
            $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
            $mail = $mailRepository->findByUid((int)$mail);
        }
        if (!self::getTyposcriptFrontendController()->fe_user->user['uid'] || $mail === null) {
            return false;
        }

        $usergroups = GeneralUtility::trimExplode(
            ',',
            self::getTyposcriptFrontendController()->fe_user->user['usergroup'],
            true
        );
        $usersSettings = GeneralUtility::trimExplode(',', $settings['edit']['feuser'], true);
        $usergroupsSettings = GeneralUtility::trimExplode(',', $settings['edit']['fegroup'], true);

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
        if (in_array(self::getTyposcriptFrontendController()->fe_user->user['uid'], $usersSettings)) {
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
    public static function isLoggedInFrontendUser()
    {
        return !empty(self::getTyposcriptFrontendController()->fe_user->user['uid']);
    }

    /**
     * Get Property from currently logged in fe_user
     *
     * @param string $propertyName
     * @return string
     */
    public static function getPropertyFromLoggedInFrontendUser($propertyName = 'uid')
    {
        $tsfe = self::getTyposcriptFrontendController();
        if (!empty($tsfe->fe_user->user[$propertyName])) {
            return $tsfe->fe_user->user[$propertyName];
        }
        return '';
    }

    /**
     * Read domain from uri
     *
     * @param string $uri
     * @return string
     */
    public static function getDomainFromUri($uri)
    {
        $uriParts = parse_url($uri);
        return $uriParts['host'];
    }

    /**
     * Get Country Name out of an IP address
     *
     * @param string $ipAddress
     * @return string Countryname
     */
    public static function getCountryFromIp($ipAddress = null)
    {
        if ($ipAddress === null) {
            // @codeCoverageIgnoreStart
            $ipAddress = GeneralUtility::getIndpEnv('REMOTE_ADDR');
            // @codeCoverageIgnoreEnd
        }
        $country = '';
        $json = GeneralUtility::getUrl('http://ip-api.com/json/' . $ipAddress);
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
        $leadingSlash = true,
        $trailingSlash = true,
        $testHost = null,
        $testUrl = null
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
}
