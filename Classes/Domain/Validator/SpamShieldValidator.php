<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\AbstractMethod;
use In2code\Powermail\Domain\Validator\SpamShield\Breaker\BreakerRunner;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\MailUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SpamShieldValidator
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
    protected $calculatedSpamFactor = 0.0;

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
    protected $messages = [];

    /**
     * @var string
     */
    protected $methodInterface = '\In2code\Powermail\Domain\Validator\SpamShield\MethodInterface';

    /**
     * @param Mail $mail
     * @return bool
     * @throws \Exception
     */
    public function isValid($mail)
    {
        if ($this->isSpamShieldEnabled($mail)) {
            $this->runAllSpamMethods($mail);
            $this->calculateMailSpamFactor();
            $this->saveSpamFactorInSession();
            $this->saveSpamPropertiesInDevelopmentLog();
            if ($this->isSpamToleranceLimitReached()) {
                $this->addError('spam_details', $this->getCalculatedSpamFactor(true));
                $this->setValidState(false);
                $this->sendSpamNotificationMail($mail);
                $this->logSpamNotification($mail);
            }
        }
        return $this->isValidState();
    }

    /**
     * @param Mail $mail
     * @return void
     * @throws \Exception
     */
    protected function runAllSpamMethods(Mail $mail)
    {
        foreach ($this->getSpamShieldMethodClasses() as $method) {
            $this->runSingleSpamMethod($mail, $method);
        }
    }

    /**
     * Run a single spam prevention method
     *
     * @param Mail $mail
     * @param array $method
     * @throws \Exception
     */
    protected function runSingleSpamMethod(Mail $mail, array $method = [])
    {
        if (!empty($method['_enable'])) {
            if (!class_exists($method['class'])) {
                throw new \UnexpectedValueException(
                    'Class ' . $method['class'] . ' does not exists - check if file was loaded with autoloader'
                );
            }
            if (is_subclass_of($method['class'], $this->methodInterface)) {
                /** @var AbstractMethod $methodInstance */
                $methodInstance = ObjectUtility::getObjectManager()->get(
                    $method['class'],
                    $mail,
                    $this->settings,
                    $this->flexForm,
                    $method['configuration']
                );
                $methodInstance->initialize();
                $methodInstance->initializeSpamCheck();
                if ((int)$method['indication'] > 0 && $methodInstance->spamCheck()) {
                    $this->increaseSpamIndicator((int)$method['indication']);
                    // @extensionScannerIgnoreLine False positive alert in TYPO3 9.5
                    $this->addMessage($method['name'] . ' failed');
                }
            } else {
                throw new \UnexpectedValueException('Spam method does not implement ' . $this->methodInterface);
            }

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
        $calculatedSpamFactor = 0;
        if ($this->getSpamIndicator() > 0) {
            $calculatedSpamFactor = -1 / $this->getSpamIndicator() + 1;
        }
        $this->setCalculatedSpamFactor($calculatedSpamFactor);
    }

    /**
     * Send spam notification mail to admin
     *
     * @param Mail $mail
     * @return void
     */
    protected function sendSpamNotificationMail(Mail $mail)
    {
        if (GeneralUtility::validEmail($this->settings['spamshield']['email'])) {
            $senderEmail = $this->settings['spamshield']['senderEmail'] ?:
                    'powermail@' . GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY');
            MailUtility::sendPlainMail(
                $this->settings['spamshield']['email'],
                $senderEmail,
                $this->settings['spamshield']['emailSubject'],
                $this->createSpamNotificationMessage(
                    $this->settings['spamshield']['emailTemplate'],
                    $this->getVariablesForSpamNotification($mail)
                )
            );
        }
    }

    /**
     * @param Mail $mail
     * @return void
     * @throws \Exception
     */
    protected function logSpamNotification(Mail $mail)
    {
        if (!empty($this->settings['spamshield']['logfileLocation'])) {
            BasicFileUtility::createFolderIfNotExists(
                BasicFileUtility::getPathFromPathAndFilename($this->settings['spamshield']['logfileLocation'])
            );
            $logMessage = $this->createSpamNotificationMessage(
                $this->settings['spamshield']['logTemplate'],
                $this->getVariablesForSpamNotification($mail)
            );
            BasicFileUtility::prependContentToFile($this->settings['spamshield']['logfileLocation'], $logMessage);
        }
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
    protected function createSpamNotificationMessage($path, $multipleAssign = [])
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($path));
        $standaloneView->assignMultiple($multipleAssign);
        return $standaloneView->render();
    }

    /**
     * Prepare variables for assignment in spam notifications
     *
     * @param Mail $mail
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getVariablesForSpamNotification(Mail $mail)
    {
        return [
            'mail' => $mail,
            'pid' => FrontendUtility::getCurrentPageIdentifier(),
            'calculatedMailSpamFactor' => $this->getCalculatedSpamFactor(true),
            'messages' => $this->getMessages(),
            'ipAddress' => $this->getIpAddress(),
            'time' => new \DateTime(),
            'request' => $_REQUEST,
            'requestPlain' => print_r($_REQUEST, true)
        ];
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
    public function getCalculatedSpamFactor($readableOutput = false)
    {
        $calculatedSpamFactor = $this->calculatedSpamFactor;
        if ($readableOutput) {
            $calculatedSpamFactor = $this->formatSpamFactor($calculatedSpamFactor);
        }
        return $calculatedSpamFactor;
    }

    /**
     * Return if spam tolerance limit is reached
     *
     * @return bool
     */
    public function isSpamToleranceLimitReached()
    {
        return $this->getCalculatedSpamFactor() >= $this->getSpamFactorLimit();
    }

    /**
     * @param float $calculatedSpamFactor
     * @return void
     */
    public function setCalculatedSpamFactor($calculatedSpamFactor)
    {
        $this->calculatedSpamFactor = $calculatedSpamFactor;
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
        $typoScriptFrontend = ObjectUtility::getTyposcriptFrontendController();
        $typoScriptFrontend->fe_user->setKey('ses', 'powermail_spamfactor', $this->getCalculatedSpamFactor(true));
        $typoScriptFrontend->storeSessionData();
    }

    /**
     * Save spam properties in development log
     *
     * @return void
     */
    protected function saveSpamPropertiesInDevelopmentLog()
    {
        if (!empty($this->settings['debug']['spamshield'])) {
            $logger = ObjectUtility::getLogger(__CLASS__);
            $logger->info('Spamshield (Spamfactor ' . $this->getCalculatedSpamFactor(true) . ')', $this->getMessages());
        }
    }

    /**
     * Get all spamshield method classes from typoscript and sort them
     *
     * @return array
     */
    protected function getSpamShieldMethodClasses()
    {
        $methods = (array)$this->settings['spamshield']['methods'];
        ksort($methods);
        return $methods;
    }

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->setSpamFactorLimit($this->settings['spamshield']['factor'] / 100);
    }

    /**
     * @return string
     */
    protected function getIpAddress()
    {
        return !ConfigurationUtility::isDisableIpLogActive() ? GeneralUtility::getIndpEnv('REMOTE_ADDR') : '';
    }

    /**
     * @param Mail $mail
     * @return bool
     */
    protected function isSpamShieldEnabled(Mail $mail): bool
    {
        $breakerRunner = ObjectUtility::getObjectManager()->get(
            BreakerRunner::class,
            $mail,
            $this->settings,
            $this->flexForm
        );
        return !empty($this->settings['spamshield']['_enable'])
            && $breakerRunner->isSpamCheckDisabledByAnyBreaker() !== true;
    }
}
