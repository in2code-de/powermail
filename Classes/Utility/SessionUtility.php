<?php

declare(strict_types=1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Validator\SpamShield\SessionMethod;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SessionUtility
 * @codeCoverageIgnore
 */
class SessionUtility
{
    /**
     * Extension Key
     */
    public static string $extKey = 'powermail';

    /**
     * Session methods
     */
    protected static array $methods = [
        'temporary' => 'ses',
        'permanently' => 'user',
    ];

    /**
     * Save current timestamp to session
     *
     * @param ?Form $form
     */
    public static function saveFormStartInSession(array $settings, Form $form = null): void
    {
        if ($form instanceof \In2code\Powermail\Domain\Model\Form && self::sessionCheckEnabled($settings)) {
            ObjectUtility::getTyposcriptFrontendController()->fe_user->setKey(
                'ses',
                'powermailFormstart' . $form->getUid(),
                time()
            );
        }
    }

    /**
     * Read form rendering timestamp from session
     *
     * @param int $formUid Form UID
     * @return int Timestamp
     */
    public static function getFormStartFromSession(int $formUid, array $settings): int
    {
        if (self::sessionCheckEnabled($settings)) {
            return (int)ObjectUtility::getTyposcriptFrontendController()->fe_user->getKey(
                'ses',
                'powermailFormstart' . $formUid
            );
        }

        return 0;
    }

    /**
     * Store Marketing Information in Session
     *        'refererDomain' => domain.org
     *        'referer' => http://domain.org/xyz/test.html
     *        'country' => Germany
     *        'mobileDevice' => 1
     *        'frontendLanguage' => 3
     *        'browserLanguage' => en-us
     *        'feUser' => userAbc
     *        'pageFunnel' => array(2, 5, 1)
     *
     * @param string $referer Referer
     * @param int $language Frontend Language Uid
     * @param int $pid Page Id
     * @param bool $mobileDevice Is mobile device?
     */
    public static function storeMarketingInformation(
        string $referer = '',
        int $language = 0,
        int $pid = 0,
        bool $mobileDevice = false,
        array $settings = []
    ): void {
        $marketingInfo = self::getSessionValue('powermail_marketing');
        // initially create array with marketing info
        if ($marketingInfo === []) {
            $marketingInfo = self::initMarketingInfo($referer, $language, $pid, $mobileDevice, $settings);
        } else {
            // add current pid to funnel
            $marketingInfo['pageFunnel'][] = $pid;

            // clean pagefunnel if has more than 256 entries
            if (count($marketingInfo['pageFunnel']) > 256) {
                $marketingInfo['pageFunnel'] = [$pid];
            }
        }

        // store in session
        self::setSessionValue('powermail_marketing', $marketingInfo, true);
    }

    /**
     * Read MarketingInfos from Session
     */
    public static function getMarketingInfos(): array
    {
        $marketingInfo = self::getSessionValue('powermail_marketing');
        if ($marketingInfo === []) {
            return self::initMarketingInfo();
        }

        return $marketingInfo;
    }

    /**
     * Save values to session for prefilling on upcoming form renderings
     *
     * @param array $settings Settings array
     */
    public static function saveSessionValuesForPrefill(Mail $mail, array $settings): void
    {
        $valuesToSave = [];
        $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
        $contentObject = ObjectUtility::getContentObject();
        $configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($settings);
        if (!empty($configuration['saveSession.']) &&
            array_key_exists($configuration['saveSession.']['_method'], self::$methods)
        ) {
            $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
            $variablesWithMarkers = $mailRepository->getVariablesWithMarkersFromMail($mail);
            $contentObject->start($variablesWithMarkers);
            foreach (array_keys($variablesWithMarkers) as $marker) {
                if (!empty($configuration['saveSession.'][$marker])) {
                    $value = $contentObject->cObjGetSingle(
                        $configuration['saveSession.'][$marker],
                        $configuration['saveSession.'][$marker . '.']
                    );
                    $valuesToSave[$marker] = $value;
                }
            }
        }

        if ($valuesToSave !== []) {
            self::setSessionValue(
                'pss',
                $valuesToSave,
                false,
                self::$methods[$configuration['saveSession.']['_method']],
                'powermailSaveSession'
            );
        }
    }

    /**
     * Get session for prefilling forms
     *
     * @param array $configuration TypoScript configuration
     */
    public static function getSessionValuesForPrefill(array $configuration): array
    {
        if (!empty($configuration['saveSession.']) &&
            array_key_exists($configuration['saveSession.']['_method'], self::$methods)) {
            return self::getSessionValue(
                'pss',
                self::$methods[$configuration['saveSession.']['_method']],
                'powermailSaveSession'
            );
        }
        return [];
    }

    public static function setCaptchaSession(string $result, int $fieldUid): void
    {
        self::setSessionValue('captcha', [$fieldUid => $result], false, 'ses', 'powermail_captcha');
    }

    public static function getCaptchaSession(int $fieldUid): int
    {
        $sessionArray = self::getSessionValue('captcha', 'ses', 'powermail_captcha');
        if (array_key_exists($fieldUid, $sessionArray)) {
            return (int)$sessionArray[$fieldUid];
        }

        return 0;
    }

    /**
     * Check if spamshield is turned on generally
     * and if ther is a sessioncheck agains spamshield enabled
     */
    protected static function sessionCheckEnabled(array $settings): bool
    {
        return ConfigurationUtility::isValidationEnabled($settings, SessionMethod::class);
    }

    /**
     * Get spam factor from session
     */
    public static function getSpamFactorFromSession(): string
    {
        return (string)ObjectUtility::getTyposcriptFrontendController()->fe_user->getKey('ses', 'powermail_spamfactor');
    }

    /**
     * Read a powermail session
     *
     * @param string $name session name
     * @param string $method "user" or "ses"
     * @param string $key name to save session
     * @return array values from session
     */
    protected static function getSessionValue(string $name = '', string $method = 'ses', string $key = ''): array
    {
        if ($key === '' || $key === '0') {
            $key = self::$extKey;
        }

        $powermailSession = ObjectUtility::getTyposcriptFrontendController()->fe_user->getKey($method, $key);
        if ($name !== '' && $name !== '0' && isset($powermailSession[$name])) {
            return $powermailSession[$name];
        }

        return [];
    }

    protected static function initMarketingInfo(
        string $referer = '',
        int $language = 0,
        int $pid = 0,
        bool $mobileDevice = false,
        array $settings = []
    ): array {
        $country = LocalizationUtility::translate('MarketingInformationCountryDisabled');
        if (isset($settings['setup']['marketing']['determineCountry'])
            && $settings['setup']['marketing']['determineCountry'] == 1) {
            $country = FrontendUtility::getCountryFromIp();
        }

        return [
            'refererDomain' => FrontendUtility::getDomainFromUri($referer),
            'referer' => $referer,
            'country' => $country,
            'mobileDevice' => $mobileDevice,
            'frontendLanguage' => $language,
            'browserLanguage' => GeneralUtility::getIndpEnv('HTTP_ACCEPT_LANGUAGE'),
            'pageFunnel' => [$pid],
        ];
    }

    /**
     * Set a powermail session and merge to old one
     *
     * @param string $name session name
     * @param array $values values to save
     * @param bool $overwrite Overwrite existing values
     * @param string $method "user" or "ses"
     * @param string $key name to save session
     */
    protected static function setSessionValue(
        string $name,
        array $values,
        bool $overwrite = false,
        string $method = 'ses',
        string $key = ''
    ): void {
        if ($key === '' || $key === '0') {
            $key = self::$extKey;
        }

        if (!$overwrite) {
            $oldValues = self::getSessionValue($name, $method, $key);
            if ($oldValues !== []) {
                $values = ArrayUtility::arrayMergeRecursiveOverrule((array)$oldValues, $values);
            }
        }

        $newValues = [
            $name => $values,
        ];
        ObjectUtility::getTyposcriptFrontendController()->fe_user->setKey($method, $key, $newValues);
    }
}
