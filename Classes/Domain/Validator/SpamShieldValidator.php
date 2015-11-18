<?php
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\MailUtility;
use In2code\Powermail\Utility\SessionUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * SpamShieldValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SpamShieldValidator extends AbstractValidator
{

    /**
     * Spam indication
     *
     * @var integer
     */
    protected $spamIndicator = 0;

    /**
     * Spam tolerance limit
     *
     * @var float
     */
    protected $spamFactorLimit = 1.0;

    /**
     * Calculated spam factor
     *
     * @var float
     */
    protected $calculatedMailSpamFactor = 0.0;

    /**
     * Referrer action
     *
     * @var string
     */
    protected $referrer;

    /**
     * Error messages for email to admin
     *
     * @var array
     */
    protected $messages = array();

    /**
     * @var TypoScriptFrontendController
     */
    protected $typoScriptFrontendController;

    /**
     * TypoScript Settings
     *
     * @var array
     */
    protected $settings;

    /**
     * Plugin arguments
     *
     * @var array
     */
    protected $piVars;

    /**
     * @var array
     */
    protected $configurationArray;

    /**
     * Spam-Validation of given Params
     *        see powermail/doc/SpamDetection for formula
     *
     * @param Mail $mail
     * @return bool
     */
    public function isValid($mail)
    {
        if (empty($this->settings['spamshield.']['_enable'])) {
            return $this->isValidState();
        }
        $this->runSpamPreventationMethods($mail);
        $this->calculateMailSpamFactor();
        $this->saveSpamFactorInSession();
        $this->saveSpamPropertiesInDevelopmentLog();

        if ($this->isSpamToleranceLimitReached()) {
            $this->addError('spam_details', $this->getCalculatedMailSpamFactor(true));
            $this->setValidState(false);
            $this->sendSpamNotificationMail($mail);
            $this->logSpamNotification($mail);
        }
        return $this->isValidState();
    }

    /**
     * Start different checks to increase spam indicator
     *
     * @param Mail $mail
     * @return void
     */
    protected function runSpamPreventationMethods($mail)
    {
        $settingsSpamShieldIndicator = $this->settings['spamshield.']['indicator.'];
        $this->honeypodCheck($settingsSpamShieldIndicator['honeypod']);
        $this->linkCheck($mail, $settingsSpamShieldIndicator['link'], $settingsSpamShieldIndicator['linkLimit']);
        $this->nameCheck($mail, $settingsSpamShieldIndicator['name']);
        $this->sessionCheck(
            $settingsSpamShieldIndicator['session'],
            SessionUtility::getFormStartFromSession($mail->getForm()->getUid(), $this->settings)
        );
        $this->uniqueCheck($mail, $settingsSpamShieldIndicator['unique']);
        $this->blacklistStringCheck($mail, $settingsSpamShieldIndicator['blacklistString']);
        $this->blacklistIpCheck($settingsSpamShieldIndicator['blacklistIp'], GeneralUtility::getIndpEnv('REMOTE_ADDR'));
    }

    /**
     * Honeypod Check: Spam recognized if Honeypod field is filled
     *
     * @param float $indication Indication if check fails
     * @return void
     */
    protected function honeypodCheck($indication = 1.0)
    {
        if (!$indication) {
            return;
        }

        if (!empty($this->piVars['field']['__hp'])) {
            $this->increaseSpamIndicator($indication);
            $this->addMessage(__FUNCTION__ . ' failed');
        }
    }

    /**
     * Link Check: Counts numbers of links in message
     *
     * @param Mail $mail
     * @param float $indication Indication if check fails
     * @param integer $limit Limit of allowed links in mail
     * @return void
     */
    protected function linkCheck(Mail $mail, $indication = 1.0, $limit = 2)
    {
        if (!$indication) {
            return;
        }

        $linkAmount = 0;
        foreach ($mail->getAnswers() as $answer) {
            if (is_array($answer->getValue())) {
                continue;
            }
            preg_match_all('@http://|https://|ftp://@', $answer->getValue(), $result);
            if (isset($result[0])) {
                $linkAmount += count($result[0]);
            }
        }

        if ($linkAmount > $limit) {
            $this->increaseSpamIndicator($indication);
            $this->addMessage(__FUNCTION__ . ' failed');
        }
    }

    /**
     * Name Check: Compares first- and lastname (shouldn't be the same)
     *
     * @param Mail $mail
     * @param float $indication Indication if check fails
     * @return void
     */
    protected function nameCheck(Mail $mail, $indication = 1.0)
    {
        if (!$indication) {
            return;
        }
        $keysFirstName = array(
            'first_name',
            'firstname',
            'vorname'
        );
        $keysLastName = array(
            'last_name',
            'lastname',
            'sur_name',
            'surname',
            'nachname',
            'name'
        );

        foreach ($mail->getAnswers() as $answer) {
            if (is_array($answer->getValue())) {
                continue;
            }
            if (in_array($answer->getField()->getMarker(), $keysFirstName)) {
                $firstname = $answer->getValue();
            }
            if (in_array($answer->getField()->getMarker(), $keysLastName)) {
                $lastname = $answer->getValue();
            }
        }

        if (!empty($firstname) && !empty($lastname) && $firstname === $lastname) {
            $this->increaseSpamIndicator($indication);
            $this->addMessage(__FUNCTION__ . ' failed');
            return;
        }
    }

    /**
     * Session Check: Checks if session was started correct on form delivery
     *
     * @param float $indication Indication if check fails
     * @param int $timeFromSession
     * @return void
     */
    protected function sessionCheck($indication = 1.0, $timeFromSession = 0)
    {
        if (!$indication || $this->referrer === 'optinConfirm') {
            return;
        }

        if (empty($timeFromSession)) {
            $this->increaseSpamIndicator($indication);
            $this->addMessage(__FUNCTION__ . ' failed');
        }
    }

    /**
     * Unique Check: Checks if values in given params are different
     *
     * @param Mail $mail
     * @param float $indication Indication if check fails
     * @return void
     */
    protected function uniqueCheck(Mail $mail, $indication = 1.0)
    {
        if (!$indication) {
            return;
        }

        $arr = array();
        foreach ($mail->getAnswers() as $answer) {

            // don't want values in second level (from checkboxes e.g.)
            if (is_array($answer->getValue())) {
                continue;
            }

            if ($answer->getValue()) {
                $arr[] = $answer->getValue();
            }
        }

        if (count($arr) !== count(array_unique($arr))) {
            $this->increaseSpamIndicator($indication);
            $this->addMessage(__FUNCTION__ . ' failed');
            return;
        }
    }

    /**
     * Blacklist String Check: Check if a blacklisted word is in given values
     *
     * @param Mail $mail
     * @param float $indication Indication if check fails
     * @return void
     */
    protected function blacklistStringCheck(Mail $mail, $indication = 1.0)
    {
        if (!$indication) {
            return;
        }
        $blacklist = GeneralUtility::trimExplode(
            ',',
            $this->settings['spamshield.']['indicator.']['blacklistStringValues'],
            true
        );

        foreach ($mail->getAnswers() as $answer) {
            if (is_array($answer->getValue())) {
                continue;
            }
            foreach ((array) $blacklist as $blackword) {
                if ($this->findStringInString($answer->getValue(), $blackword)) {
                    $this->increaseSpamIndicator($indication);
                    $this->addMessage(__FUNCTION__ . ' failed');
                    return;
                }
            }
        }
    }

    /**
     * Blacklist IP-Address Check: Check if Senders IP is blacklisted
     *
     * @param float $indication Indication if check fails
     * @param string $userIpAddress Visitors IP address
     * @return void
     */
    protected function blacklistIpCheck($indication = 1.0, $userIpAddress = '')
    {
        if (!$indication) {
            return;
        }
        $blacklist = GeneralUtility::trimExplode(
            ',',
            $this->settings['spamshield.']['indicator.']['blacklistIpValues'],
            true
        );

        if (in_array($userIpAddress, $blacklist)) {
            $this->increaseSpamIndicator($indication);
            $this->addMessage(__FUNCTION__ . ' failed');
            return;
        }
    }

    /**
     * calculate spam factor for this mail
     *        spam formula with asymptote 1 (100%)
     *
     * @return void
     */
    protected function calculateMailSpamFactor()
    {
        $calculatedMailSpamFactor = 0;
        if ($this->getSpamIndicator() > 0) {
            $calculatedMailSpamFactor = -1 / $this->getSpamIndicator() + 1;
        }
        $this->setCalculatedMailSpamFactor($calculatedMailSpamFactor);
    }

    /**
     * Send spam notification mail to admin
     *
     * @param Mail $mail
     * @return void
     */
    protected function sendSpamNotificationMail(Mail $mail)
    {
        if (!GeneralUtility::validEmail($this->settings['spamshield.']['email'])) {
            return;
        }
        MailUtility::sendPlainMail(
            $this->settings['spamshield.']['email'],
            'powermail@' . GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY'),
            $this->settings['spamshield.']['emailSubject'],
            $this->createSpamNotificationMessage(
                $this->settings['spamshield.']['emailTemplate'],
                $this->getVariablesForSpamNotification($mail)
            )
        );
    }

    /**
     * Log Spam Notification
     *
     * @param Mail $mail
     * @return void
     */
    protected function logSpamNotification(Mail $mail)
    {
        if (empty($this->settings['spamshield.']['logfileLocation'])) {
            return;
        }
        BasicFileUtility::createFolderIfNotExists(
            BasicFileUtility::getPathFromPathAndFilename($this->settings['spamshield.']['logfileLocation'])
        );
        $logMessage = $this->createSpamNotificationMessage(
            $this->settings['spamshield.']['logTemplate'],
            $this->getVariablesForSpamNotification($mail)
        );
        BasicFileUtility::prependContentToFile($this->settings['spamshield.']['logfileLocation'], $logMessage);
    }

    /**
     * Create message for spam logging
     *        - bodytext for spamnotification mail OR
     *        - log entry
     *
     * @param string $path relative path to mail
     * @param array $multipleAssign
     * @return string
     */
    protected function createSpamNotificationMessage($path, $multipleAssign = array())
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($path));
        $standaloneView->setLayoutRootPaths(TemplateUtility::getTemplateFolders('layout'));
        $standaloneView->setPartialRootPaths(TemplateUtility::getTemplateFolders('partial'));
        $standaloneView->assignMultiple($multipleAssign);
        return $standaloneView->render();
    }

    /**
     * Prepare variables for assignment in spam notifications
     *
     * @param Mail $mail
     * @return array
     */
    protected function getVariablesForSpamNotification(Mail $mail)
    {
        return array(
            'mail' => $mail,
            'pid' => FrontendUtility::getCurrentPageIdentifier(),
            'calculatedMailSpamFactor' => $this->getCalculatedMailSpamFactor(true),
            'messages' => $this->getMessages(),
            'ipAddress' =>
                (!ConfigurationUtility::isDisableIpLogActive() ? GeneralUtility::getIndpEnv('REMOTE_ADDR') : ''),
            'time' => new \DateTime()
        );
    }

    /**
     * Format for Spamfactor (0.23 => 23%)
     *
     * @param float $factor
     * @return string
     */
    protected function formatSpamFactor($factor)
    {
        return number_format(($factor * 100), 0) . '%';
    }

    /**
     * @param int $spamIndicator
     * @return void
     */
    public function setSpamIndicator($spamIndicator)
    {
        $this->spamIndicator = $spamIndicator;
    }

    /**
     * @return int
     */
    public function getSpamIndicator()
    {
        return $this->spamIndicator;
    }

    /**
     * Increase Global Indicator
     *
     * @param int $indication
     * @return void
     */
    public function increaseSpamIndicator($indication)
    {
        $this->setSpamIndicator($this->getSpamIndicator() + $indication);
    }

    /**
     * @return float
     */
    public function getSpamFactorLimit()
    {
        return $this->spamFactorLimit;
    }

    /**
     * @param float $spamFactorLimit
     * @return void
     */
    public function setSpamFactorLimit($spamFactorLimit)
    {
        $this->spamFactorLimit = $spamFactorLimit;
    }

    /**
     * @param bool $readableOutput
     * @return float
     */
    public function getCalculatedMailSpamFactor($readableOutput = false)
    {
        $calculatedMailSpamFactor = $this->calculatedMailSpamFactor;
        if ($readableOutput) {
            $calculatedMailSpamFactor = $this->formatSpamFactor($calculatedMailSpamFactor);
        }
        return $calculatedMailSpamFactor;
    }

    /**
     * Return if spam tolerance limit is reached
     *
     * @return bool
     */
    public function isSpamToleranceLimitReached()
    {
        return $this->getCalculatedMailSpamFactor() >= $this->getSpamFactorLimit();
    }

    /**
     * @param float $calculatedMailSpamFactor
     * @return void
     */
    public function setCalculatedMailSpamFactor($calculatedMailSpamFactor)
    {
        $this->calculatedMailSpamFactor = $calculatedMailSpamFactor;
    }

    /**
     * @param array $messages
     * @return void
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Add $message
     *
     * @param $message
     * @return void
     */
    public function addMessage($message)
    {
        $messages = $this->getMessages();
        $messages[] = $message;
        $this->setMessages($messages);
    }

    /**
     * Save Spam Factor in session for db storage
     *
     * @return void
     */
    protected function saveSpamFactorInSession()
    {
        $this->typoScriptFrontendController->fe_user->setKey(
            'ses',
            'powermail_spamfactor',
            $this->getCalculatedMailSpamFactor(true)
        );
        $this->typoScriptFrontendController->storeSessionData();
    }

    /**
     * Save spam properties in development log
     *
     * @return void
     */
    protected function saveSpamPropertiesInDevelopmentLog()
    {
        if (empty($this->settings['debug.']['spamshield'])) {
            return;
        }
        GeneralUtility::devLog(
            'Spamshield (Spamfactor ' . $this->getCalculatedMailSpamFactor(true) . ')',
            'powermail',
            0,
            $this->getMessages()
        );
    }

    /**
     * Find string in string but only if it's alone
     * Search for "sex":
     *        "Sex" => TRUE
     *        "test sex test" => TRUE
     *        "Staatsexamen" => FALSE
     *        "_sex_bla" => TRUE
     *        "tst sex.seems.to.be.nice" => TRUE
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    protected function findStringInString($haystack, $needle)
    {
        return preg_match('/(?:\A|[\s\b_-]|\.)' . $needle . '(?:$|[\s\b_-]|\.)/i', $haystack) === 1;
    }

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->piVars = GeneralUtility::_GP('tx_powermail_pi1');
        $this->referrer = $this->piVars['__referrer']['@action'];
        $this->typoScriptFrontendController = $GLOBALS['TSFE'];
        $this->configurationArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
        $this->setSpamFactorLimit($this->settings['spamshield.']['factor'] / 100);
    }
}
