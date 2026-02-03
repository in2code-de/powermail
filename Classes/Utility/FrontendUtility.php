<?php

declare(strict_types=1);
namespace In2code\Powermail\Utility;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Repository\UserRepository;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\Client\GuzzleClientFactory;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     */
    public static function getCurrentPageIdentifier(): int
    {
        if (self::getRequest() !== null) {
            return self::getRequest()->getAttribute('frontend.page.information')->getId() ?? 0;
        }
        return 0;
    }

    /**
     * Get configured frontend language
     */
    public static function getSysLanguageUid(): int
    {
        if (self::getRequest() !== null) {
            return self::getRequest()->getAttribute('language')->getLanguageId();
        }
        return 0;
    }

    public static function getPluginName(): string
    {
        $pluginName = 'tx_powermail_pi1';
        if (self::isArgumentExisting('tx_powermail_pi2')) {
            $pluginName = 'tx_powermail_pi2';
        } elseif (self::isArgumentExisting('tx_powermail_pi3')) {
            $pluginName = 'tx_powermail_pi3';
        } elseif (self::isArgumentExisting('tx_powermail_pi4')) {
            $pluginName = 'tx_powermail_pi4';
        }
        if (self::isArgumentExisting('tx_powermail_web_powermailm1')) {
            return 'tx_powermail_web_powermailm1';
        }

        return $pluginName;
    }

    public static function getActionName(): string
    {
        $action = '';
        $arguments = self::getArguments(self::getPluginName());
        if (!empty($arguments['action'])) {
            return $arguments['action'];
        }

        return $action;
    }

    /**
     * Check if logged in user is allowed to make changes in Pi2
     *
     * @param array $settings $settings TypoScript and Flexform Settings
     * @param int|Mail $mail
     * @throws DBALException
     * @codeCoverageIgnore
     */
    public static function isAllowedToEdit(array $settings, $mail): bool
    {
        if (!is_a($mail, Mail::class)) {
            $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
            $mail = $mailRepository->findByUid((int)$mail);
        }

        $request = self::getRequest();
        if ($request === null) {
            return false;
        }

        $feUser = $request->getAttribute('frontend.user')->getUserId();
        if ($feUser === 0 || $mail === null) {
            return false;
        }

        $usergroups = $request->getAttribute('frontend.user')->createUserAspect()->getGroupIds();
        $usersSettings = GeneralUtility::trimExplode(',', $settings['edit']['feuser'] ?? '', true);
        $usergroupsSettings = GeneralUtility::trimExplode(',', $settings['edit']['fegroup'] ?? '', true);

        // replace "_owner" with uid of owner in array with users
        if ($mail->getFeuser() !== null && is_numeric(array_search('_owner', $usersSettings, true))) {
            $usersSettings[array_search('_owner', $usersSettings, true)] = $mail->getFeuser()->getUid();
        }

        // add owner groups to allowed groups (if "_owner")
        if ($mail->getFeuser() !== null && is_numeric(array_search('_owner', $usergroupsSettings, true))) {
            $userRepository = GeneralUtility::makeInstance(UserRepository::class);
            $usergroupsFromOwner = $userRepository->getUserGroupsFromUser($mail->getFeuser()->getUid());
            $usergroupsSettings = array_merge($usergroupsSettings, (array)$usergroupsFromOwner);
        }

        // 1. check user
        if (in_array($feUser, $usersSettings)) {
            return true;
        }
        // 2. check usergroup
        return (bool)count(array_intersect($usergroups, $usergroupsSettings));
    }

    public static function isAllowedToView(array $settings, Mail $mail): bool
    {
        $request = self::getRequest();

        if ($request === null) {
            return false;
        }

        $feUser = $request->getAttribute('frontend.user')->getUserId();
        if (
            $feUser === 0 ||
            (
                (int)$settings['list']['showownonly'] === 1
                && $mail->getFeuser()->getUid() !== $feUser
            )
        ) {
            return false;
        }

        return true;
    }

    /**
     * Is a frontend user logged in
     */
    public static function isLoggedInFrontendUser(): bool
    {
        return (int)self::getPropertyFromLoggedInFrontendUser() > 0;
    }

    /**
     * Get Property from currently logged in fe_user
     */
    public static function getPropertyFromLoggedInFrontendUser(string $propertyName = 'uid'): string
    {
        $request = self::getRequest();
        if ($request !== null) {
            $feUser = $request->getAttribute('frontend.user');
            return (string)($feUser->user[$propertyName] ?? '');
        }
        return '';
    }

    /**
     * Read domain from uri
     */
    public static function getDomainFromUri(string $uri): string
    {
        $uriParts = parse_url($uri);
        return (string)($uriParts['host'] ?? '');
    }

    public static function getCountryFromIp(?string $ipAddress = null): string
    {
        if ($ipAddress === null) {
            // @codeCoverageIgnoreStart
            $ipAddress = GeneralUtility::getIndpEnv('REMOTE_ADDR');
            // @codeCoverageIgnoreEnd
        }

        $country = '';
        $guzzleFactory = GeneralUtility::makeInstance(GuzzleClientFactory::class);
        $json = GeneralUtility::makeInstance(RequestFactory::class, $guzzleFactory)
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
     * @param ?string $testHost can be used for a test
     * @param ?string $testUrl can be used for a test
     */
    public static function getSubFolderOfCurrentUrl(
        bool $leadingSlash = true,
        bool $trailingSlash = true,
        ?string $testHost = null,
        ?string $testUrl = null
    ): string {
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
            return '/' . $subfolder;
        }

        return $subfolder;
    }

    public static function isArgumentExisting(string $key): bool
    {
        return self::getArguments($key) !== [];
    }

    /**
     * Because GET params can be rewritten via routing configuration and so only available via TSFE on the one hand
     * and on the other hand some POST params are still available when a form is submitted, we need a function that
     * merges both sources
     */
    public static function getArguments(string $key = 'tx_powermail_pi1'): array
    {
        return array_merge(
            self::getArgumentsFromTyposcriptFrontendController($key),
            self::getArgumentsFromGetOrPostRequest($key)
        );
    }

    protected static function getArgumentsFromGetOrPostRequest(string $key): array
    {
        return $GLOBALS['TYPO3_REQUEST']->getParsedBody()[$key] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()[$key] ?? [];
    }

    protected static function getArgumentsFromTyposcriptFrontendController(string $key): array
    {
        $request = self::getRequest();
        if ($request !== null) {
            $pageArguments = $request->getAttribute('routing');
            $arguments = $pageArguments->getArguments();
            if (array_key_exists($key, $arguments)) {
                return (array)$arguments[$key];
            }
        }
        return [];
    }

    protected static function getRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }
}
