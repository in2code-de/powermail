<?php
declare(strict_types = 1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Validator\SpamShield\SessionMethod;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class SessionUtility
 * @codeCoverageIgnore
 */
class SessionUtility
{

    /**
     * Extension Key
     */
    public static $extKey = 'powermail';

    /**
     * Session methods
     *
     * @var array
     */
    protected static $methods = [
        'temporary' => 'ses',
        'permanently' => 'user'
    ];

    /**
     * Save current timestamp to session
     *
     * @param Form $form
     * @param array $settings
     * @return void
     */
    public static function saveFormStartInSession(array $settings, Form $form = null): void
    {
        if ($form !== null && self::sessionCheckEnabled($settings)) {
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
     * @param array $settings
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
     * @return void
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
        if (empty($marketingInfo)) {
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
     *
     * @return array
     */
    public static function getMarketingInfos(): array
    {
        $marketingInfo = self::getSessionValue('powermail_marketing');
        if (empty($marketingInfo)) {
            $marketingInfo = self::initMarketingInfo();
        }
        return $marketingInfo;
    }

    /**
     * Save values to session for prefilling on upcoming form renderings
     *
     * @param Mail $mail
     * @param array $settings Settings array
     * @return void
     * @throws Exception
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public static function saveSessionValuesForPrefill(Mail $mail, array $settings): void
    {
        $valuesToSave = [];
        $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
        $contentObject = ObjectUtility::getContentObject();
        $configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($settings);
        if (!empty($configuration['saveSession.']) &&
            array_key_exists($configuration['saveSession.']['_method'], self::$methods)
        ) {
            $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
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
        if (count($valuesToSave)) {
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
     * @return array
     */
    public static function getSessionValuesForPrefill(array $configuration): array
    {
        $values = [];
        if (!empty($configuration['saveSession.']) &&
            array_key_exists($configuration['saveSession.']['_method'], self::$methods)
        ) {
            $values = self::getSessionValue(
                'pss',
                self::$methods[$configuration['saveSession.']['_method']],
                'powermailSaveSession'
            );
        }

        return $values;
    }

    /**
     * @param string $result
     * @param int $fieldUid
     * @return void
     */
    public static function setCaptchaSession(string $result, int $fieldUid): void
    {
        self::setSessionValue('captcha', [$fieldUid => $result], false, 'ses', 'powermail_captcha');
    }

    /**
     * @param int $fieldUid
     * @return int
     */
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
     *
     * @param array $settings
     * @return bool
     */
    protected static function sessionCheckEnabled(array $settings): bool
    {
        return ConfigurationUtility::isValidationEnabled($settings, SessionMethod::class);
    }

    /**
     * Get spam factor from session
     *
     * @return string
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
        if (empty($key)) {
            $key = self::$extKey;
        }
        $powermailSession = ObjectUtility::getTyposcriptFrontendController()->fe_user->getKey($method, $key);
        if (!empty($name) && isset($powermailSession[$name])) {
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
    ) {
        $country = LocalizationUtility::translate('MarketingInformationCountryDisabled');
        if (isset($settings['setup']['marketing']['determineCountry']) && $settings['setup']['marketing']['determineCountry'] == 1) {
            $country = FrontendUtility::getCountryFromIp();
        }
        $marketingInfo = [
            'refererDomain' => FrontendUtility::getDomainFromUri($referer),
            'referer' => $referer,
            'country' => $country,
            'mobileDevice' => $mobileDevice,
            'frontendLanguage' => $language,
            'browserLanguage' => GeneralUtility::getIndpEnv('HTTP_ACCEPT_LANGUAGE'),
            'pageFunnel' => [$pid]
        ];

        return $marketingInfo;
    }

    /**
     * Set a powermail session and merge to old one
     *
     * @param string $name session name
     * @param array $values values to save
     * @param bool $overwrite Overwrite existing values
     * @param string $method "user" or "ses"
     * @param string $key name to save session
     * @return void
     */
    protected static function setSessionValue(
        string $name,
        array $values,
        bool $overwrite = false,
        string $method = 'ses',
        string $key = ''
    ): void {
        if (empty($key)) {
            $key = self::$extKey;
        }
        if (!$overwrite) {
            $oldValues = self::getSessionValue($name, $method, $key);
            if ($oldValues) {
                $values = ArrayUtility::arrayMergeRecursiveOverrule((array)$oldValues, (array)$values);
            }
        }
        $newValues = [
            $name => $values
        ];
        ObjectUtility::getTyposcriptFrontendController()->fe_user->setKey($method, $key, $newValues);
    }
}
