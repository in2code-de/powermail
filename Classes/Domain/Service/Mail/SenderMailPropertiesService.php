<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Events\SenderMailPropertiesGetSenderEmailEvent;
use In2code\Powermail\Events\SenderMailPropertiesGetSenderNameEvent;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;

/**
 * Class SenderMailPropertiesService to get email array for sender attributes
 * for sender emails
 */
class SenderMailPropertiesService
{
    /**
     * TypoScript configuration for cObject parsing
     */
    protected array $configuration = [];

    private readonly EventDispatcherInterface $eventDispatcher;

    public function __construct(/**
     * TypoScript settings as plain array
     */
        protected array $settings
    ) {
        $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
        $this->configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
        $this->eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
    }

    /**
     * Get sender email from form settings. If empty, take default from TypoScript or TYPO3 configuration
     *
     * @throws ExceptionExtbaseObject
     */
    public function getSenderEmail(): string
    {
        if ($this->settings['sender']['email'] !== '') {
            $senderEmail = $this->settings['sender']['email'];
        } else {
            $senderEmail = ConfigurationUtility::getDefaultMailFromAddress();
            $senderEmail = TypoScriptUtility::overwriteValueFromTypoScript(
                $senderEmail,
                $this->configuration['sender.']['default.'],
                'senderEmail'
            );
        }

        /** @var SenderMailPropertiesGetSenderEmailEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new SenderMailPropertiesGetSenderEmailEvent($senderEmail, $this)
        );
        return $event->getSenderEmail();
    }

    /**
     * Get sender name from form settings. If empty, take default from TypoScript or TYPO3 configuration.
     *
     * @throws ExceptionExtbaseObject
     */
    public function getSenderName(): string
    {
        if ($this->settings['sender']['name'] !== '') {
            $senderName = $this->settings['sender']['name'];
        } else {
            $senderName = ConfigurationUtility::getDefaultMailFromName();
            $senderName = TypoScriptUtility::overwriteValueFromTypoScript(
                $senderName,
                $this->configuration['sender.']['default.'],
                'senderName'
            );
        }

        /** @var SenderMailPropertiesGetSenderNameEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new SenderMailPropertiesGetSenderNameEvent($senderName, $this)
        );
        return $event->getSenderName();
    }
}
