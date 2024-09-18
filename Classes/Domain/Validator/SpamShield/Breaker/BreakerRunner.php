<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield\Breaker;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Exception\ClassDoesNotExistException;
use In2code\Powermail\Exception\ConfigurationIsMissingException;
use In2code\Powermail\Exception\InterfaceNotImplementedException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BreakerRunner
 */
class BreakerRunner
{
    /**
     * @var string
     */
    protected string $interface = BreakerInterface::class;

    /**
     * @var ?Mail
     */
    protected ?Mail $mail = null;

    /**
     * @var array
     */
    protected array $settings = [];

    /**
     * @var array
     */
    protected array $flexForm = [];

    /**
     * @param Mail $mail
     * @param array $settings
     * @param array $flexForm
     */
    public function __construct(Mail $mail, array $settings, array $flexForm)
    {
        $this->mail = $mail;
        $this->settings = $settings;
        $this->flexForm = $flexForm;
    }

    /**
     * @return bool
     * @throws ClassDoesNotExistException
     * @throws ConfigurationIsMissingException
     * @throws InterfaceNotImplementedException
     */
    public function isSpamCheckDisabledByAnyBreaker(): bool
    {
        foreach ($this->getBreaker() as $breaker) {
            if (!isset($breaker['class'])) {
                throw new ConfigurationIsMissingException(
                    'Setup ...spamshield.disable.NO.class not given in TypoScript',
                    1516024297083
                );
            }
            if (!class_exists($breaker['class'])) {
                throw new ClassDoesNotExistException(
                    'Class ' . $breaker['class'] . ' does not exists - check if file was loaded with autoloader',
                    1516024305363
                );
            }
            if (!is_subclass_of($breaker['class'], $this->interface)) {
                throw new InterfaceNotImplementedException(
                    'Breaker method does not implement ' . $this->interface,
                    1516024315548
                );
            }
            /** @var AbstractBreaker $breakerInstance */
            $breakerInstance = GeneralUtility::makeInstance(
                $breaker['class'],
                $this->mail,
                $this->settings,
                $this->flexForm,
                $breaker['configuration'] ?? []
            );
            $breakerInstance->initialize();
            if ($breakerInstance->isDisabled() === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    protected function getBreaker(): array
    {
        $breakerConfiguration = [];
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $settings = $configurationService->getTypoScriptSettings();
        if (!empty($settings['spamshield']['_disable'])) {
            $breakerConfiguration = $settings['spamshield']['_disable'];
        }
        return $breakerConfiguration;
    }
}
