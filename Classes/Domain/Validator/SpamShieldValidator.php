<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use Exception;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\AbstractMethod;
use In2code\Powermail\Domain\Validator\SpamShield\Breaker\BreakerRunner;
use In2code\Powermail\Domain\Validator\SpamShield\MethodInterface;
use In2code\Powermail\Exception\ClassDoesNotExistException;
use In2code\Powermail\Exception\InterfaceNotImplementedException;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\MailUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;

/**
 * Class SpamShieldValidator
 */
class SpamShieldValidator extends AbstractValidator
{
    /**
     * Spam indication
     */
    protected int $spamIndicator = 0;

    /**
     * Spam tolerance limit
     */
    protected float $spamFactorLimit = 1.0;

    /**
     * Calculated spam factor
     */
    protected float $calculatedSpamFactor = 0.0;

    /**
     * Error messages for email to admin
     */
    protected array $messages = [];

    protected string $methodInterface = MethodInterface::class;

    /**
     * @param Mail $mail
     * @return Result
     * @throws Exception
     */
    public function validate($mail): Result
    {
        $this->result = new Result();
        if ($this->isSpamShieldEnabled($mail)) {
            $this->isValid($mail);
        }

        return $this->result;
    }

    /**
     * @throws InvalidExtensionNameException
     */
    protected function isValid($mail): void
    {
        $this->initFlexform();
        $this->runAllSpamMethods($mail);
        $this->calculateMailSpamFactor();
        $this->saveSpamFactorInSession();
        if ($this->isSpamToleranceLimitReached()) {
            $this->addError('spam_details', 1580681599, ['spamfactor' => $this->getCalculatedSpamFactor(true)]);
            $this->setValidState(false);
            $this->sendSpamNotificationMail($mail);
            $this->logSpamNotification($mail);
        }
    }

    /**
     * @throws Exception
     */
    protected function runAllSpamMethods(Mail $mail): void
    {
        foreach ($this->getSpamShieldMethodClasses() as $method) {
            $this->runSingleSpamMethod($mail, $method);
        }
    }

    /**
     * Run a single spam prevention method
     *
     * @throws ClassDoesNotExistException
     * @throws InterfaceNotImplementedException
     */
    protected function runSingleSpamMethod(Mail $mail, array $method = []): void
    {
        if (!empty($method['_enable'])) {
            if (!class_exists($method['class'])) {
                throw new ClassDoesNotExistException(
                    'Class ' . $method['class'] . ' does not exists - check if file was loaded with autoloader',
                    1578609568
                );
            }

            if (is_subclass_of($method['class'], $this->methodInterface)) {
                /** @var AbstractMethod $methodInstance */
                $methodInstance = GeneralUtility::makeInstance(
                    $method['class'],
                    $mail,
                    $this->settings,
                    $this->flexForm,
                    $method['configuration'] ?? []
                );
                $methodInstance->initialize();
                $methodInstance->initializeSpamCheck();
                if ((int)$method['indication'] > 0 && $methodInstance->spamCheck()) {
                    $this->increaseSpamIndicator((int)$method['indication']);
                    // @extensionScannerIgnoreLine False positive alert in TYPO3 9.5
                    $this->addMessage($method['name'] . ' failed');
                }
            } else {
                throw new InterfaceNotImplementedException(
                    'Spam method does not implement ' . $this->methodInterface,
                    1578609554
                );
            }
        }
    }

    /**
     * calculate spam factor for this mail
     *        spam formula with asymptote 1 (100%)
     */
    protected function calculateMailSpamFactor(): void
    {
        $calculatedSpamFactor = 0;
        if ($this->getSpamIndicator() > 0) {
            $calculatedSpamFactor = -1 / $this->getSpamIndicator() + 1;
        }

        $this->setCalculatedSpamFactor($calculatedSpamFactor);
    }

    /**
     * Send spam notification mail to admin
     * @throws InvalidExtensionNameException
     */
    protected function sendSpamNotificationMail(Mail $mail): void
    {
        if (GeneralUtility::validEmail($this->settings['spamshield']['email'] ?? '')) {
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
     * @throws Exception
     */
    protected function logSpamNotification(Mail $mail): void
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
     *     - bodytext for spamnotification mail OR
     *     - log entry
     * @param string $path
     * @param array $multipleAssign
     * @return string
     */
    protected function createSpamNotificationMessage(string $path, array $multipleAssign = []): string
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($path));
        $standaloneView->assignMultiple($multipleAssign);
        return $standaloneView->render();
    }

    /**
     * Prepare variables for assignment in spam notifications
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @throws Exception
     */
    protected function getVariablesForSpamNotification(Mail $mail): array
    {
        return [
            'mail' => $mail,
            'pid' => FrontendUtility::getCurrentPageIdentifier(),
            'calculatedMailSpamFactor' => $this->getCalculatedSpamFactor(true),
            'messages' => $this->getMessages(),
            'ipAddress' => $this->getIpAddress(),
            'time' => new \DateTime(),
            'request' => $_REQUEST,
            'requestPlain' => print_r($_REQUEST, true),
        ];
    }

    /**
     * Format for Spamfactor (0.23 => 23%)
     */
    protected function formatSpamFactor(float $factor): string
    {
        return number_format(($factor * 100), 0) . '%';
    }

    public function setSpamIndicator(int $spamIndicator): void
    {
        $this->spamIndicator = $spamIndicator;
    }

    public function getSpamIndicator(): int
    {
        return $this->spamIndicator;
    }

    /**
     * Increase Global Indicator
     */
    public function increaseSpamIndicator(int $indication): void
    {
        $this->setSpamIndicator($this->getSpamIndicator() + $indication);
    }

    public function getSpamFactorLimit(): float
    {
        return $this->spamFactorLimit;
    }

    public function setSpamFactorLimit(float $spamFactorLimit): void
    {
        $this->spamFactorLimit = $spamFactorLimit;
    }

    public function getCalculatedSpamFactor(bool $readableOutput = false): string|float
    {
        $calculatedSpamFactor = $this->calculatedSpamFactor;
        if ($readableOutput) {
            return $this->formatSpamFactor($calculatedSpamFactor);
        }

        return $calculatedSpamFactor;
    }

    /**
     * Return if spam tolerance limit is reached
     */
    public function isSpamToleranceLimitReached(): bool
    {
        return $this->getCalculatedSpamFactor() >= $this->getSpamFactorLimit();
    }

    public function setCalculatedSpamFactor(float $calculatedSpamFactor): void
    {
        $this->calculatedSpamFactor = $calculatedSpamFactor;
    }

    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function addMessage(string $message): void
    {
        $messages = $this->getMessages();
        $messages[] = $message;
        $this->setMessages($messages);
    }

    /**
     * Save Spam Factor in session for db storage
     */
    protected function saveSpamFactorInSession(): void
    {
        if ($this->request !== null) {
            $fe_user = $this->request->getAttribute('frontend.user');
            $fe_user->setKey('ses', 'powermail_spamfactor', $this->getCalculatedSpamFactor(true));
        }
    }

    /**
     * Get all spamshield method classes from typoscript and sort them
     */
    protected function getSpamShieldMethodClasses(): array
    {
        $methods = (array)$this->settings['spamshield']['methods'];
        ksort($methods);
        return $methods;
    }

    public function initializeObject(): void
    {
        $this->setSpamFactorLimit((int)($this->settings['spamshield']['factor'] ?? 0) / 100);
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected function getIpAddress(): string
    {
        return ConfigurationUtility::isDisableIpLogActive() ? '' : GeneralUtility::getIndpEnv('REMOTE_ADDR');
    }

    /**
     * @throws Exception
     */
    protected function isSpamShieldEnabled(Mail $mail): bool
    {
        $this->initializeObject();
        $breakerRunner = GeneralUtility::makeInstance(
            BreakerRunner::class,
            $mail,
            $this->settings,
            $this->flexForm
        );
        return !empty($this->settings['spamshield']['_enable'])
            && $breakerRunner->isSpamCheckDisabledByAnyBreaker() !== true;
    }
}
