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
class SessionUtility extends AbstractUtility
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
    public static function saveFormStartInSession(array $settings, Form $form = null)
    {
        if ($form !== null && self::sessionCheckEnabled($settings)) {
            self::getTyposcriptFrontendController()->fe_user->setKey(
                'ses',
                'powermailFormstart' . $form->getUid(),
                time()
            );
            self::getTyposcriptFrontendController()->storeSessionData();
        }
    }

    /**
     * Read form rendering timestamp from session
     *
     * @param integer $formUid Form UID
     * @param array $settings
     * @return integer Timestamp
     */
    public static function getFormStartFromSession($formUid, array $settings)
    {
        if (self::sessionCheckEnabled($settings)) {
            return (int)self::getTyposcriptFrontendController()->fe_user->getKey(
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
     * @param int $mobileDevice Is mobile device?
     * @return void
     */
    public static function storeMarketingInformation($referer = null, $language = 0, $pid = 0, $mobileDevice = 0)
    {
        $marketingInfo = self::getSessionValue('powermail_marketing');

        // initially create array with marketing info
        if (!is_array($marketingInfo)) {
            $marketingInfo = [
                'refererDomain' => FrontendUtility::getDomainFromUri($referer),
                'referer' => $referer,
                'country' => FrontendUtility::getCountryFromIp(),
                'mobileDevice' => $mobileDevice,
                'frontendLanguage' => $language,
                'browserLanguage' => GeneralUtility::getIndpEnv('HTTP_ACCEPT_LANGUAGE'),
                'pageFunnel' => [$pid]
            ];
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
    public static function getMarketingInfos()
    {
        $marketingInfo = self::getSessionValue('powermail_marketing');
        if (!is_array($marketingInfo)) {
            $marketingInfo = [
                'refererDomain' => '',
                'referer' => '',
                'country' => '',
                'mobileDevice' => 0,
                'frontendLanguage' => 0,
                'browserLanguage' => '',
                'pageFunnel' => []
            ];
        }
        return $marketingInfo;
    }

    /**
     * Save values to session for prefilling on upcoming form renderings
     *
     * @param Mail $mail
     * @param array $settings Settings array
     * @return void
     */
    public static function saveSessionValuesForPrefill(Mail $mail, $settings)
    {
        $valuesToSave = [];
        $typoScriptService = self::getObjectManager()->get(TypoScriptService::class);
        $contentObject = self::getContentObject();
        $configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($settings);
        if (!empty($configuration['saveSession.']) &&
            array_key_exists($configuration['saveSession.']['_method'], self::$methods)
        ) {
            $mailRepository = self::getObjectManager()->get(MailRepository::class);
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
    public static function getSessionValuesForPrefill($configuration)
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
    public static function setCaptchaSession($result, $fieldUid)
    {
        self::setSessionValue('captcha', [$fieldUid => $result], false, 'ses', 'powermail_captcha');
    }

    /**
     * @param int $fieldUid
     * @return int
     */
    public static function getCaptchaSession($fieldUid)
    {
        $sessionArray = self::getSessionValue('captcha', 'ses', 'powermail_captcha');
        return (int)$sessionArray[$fieldUid];
    }

    /**
     * Check if spamshield is turned on generally
     * and if ther is a sessioncheck agains spamshield enabled
     *
     * @param array $settings
     * @return bool
     */
    protected static function sessionCheckEnabled(array $settings)
    {
        return ConfigurationUtility::isValidationEnabled($settings, SessionMethod::class);
    }

    /**
     * Get spam factor from session
     *
     * @return string
     */
    public static function getSpamFactorFromSession()
    {
        return self::getTyposcriptFrontendController()->fe_user->getKey('ses', 'powermail_spamfactor');
    }

    /**
     * Read a powermail session
     *
     * @param string $name session name
     * @param string $method "user" or "ses"
     * @param string $key name to save session
     * @return string Values from session
     */
    protected static function getSessionValue($name = '', $method = 'ses', $key = '')
    {
        if (empty($key)) {
            $key = self::$extKey;
        }
        $powermailSession = self::getTyposcriptFrontendController()->fe_user->getKey($method, $key);
        if (!empty($name) && isset($powermailSession[$name])) {
            return $powermailSession[$name];
        }
        return '';
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
    protected static function setSessionValue($name, $values, $overwrite = false, $method = 'ses', $key = '')
    {
        if (empty($key)) {
            $key = self::$extKey;
        }
        if (!$overwrite) {
            $oldValues = self::getSessionValue($name, $method, $key);
            if (!empty($oldValues)) {
                $values = ArrayUtility::arrayMergeRecursiveOverrule((array)$oldValues, (array)$values);
            }
        }
        $newValues = [
            $name => $values
        ];
        self::getTyposcriptFrontendController()->fe_user->setKey($method, $key, $newValues);
        self::getTyposcriptFrontendController()->storeSessionData();
    }
}
