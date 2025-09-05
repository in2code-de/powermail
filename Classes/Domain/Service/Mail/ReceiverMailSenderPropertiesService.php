<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Events\ReceiverMailSenderPropertiesGetSenderEmailEvent;
use In2code\Powermail\Events\ReceiverMailSenderPropertiesGetSenderNameEvent;
use In2code\Powermail\Utility\TypoScriptUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;

/**
 * Class ReceiverMailSenderPropertiesService to get email array for sender attributes
 */
class ReceiverMailSenderPropertiesService
{
    /**
     * @var MailRepository
     */
    protected object $mailRepository;

    /**
     * TypoScript configuration for cObject parsing
     */
    protected array $configuration = [];

    private readonly EventDispatcherInterface $eventDispatcher;

    /**
     * @param Mail $mail
     */
    public function __construct(protected ?Mail $mail, /**
     * TypoScript settings as plain array
     */
        protected array $settings)
    {
        $this->mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
        $this->configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
        $this->eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
    }

    /**
     * Get sender email from configuration in fields and params. If empty, take default from TypoScript
     *
     * @throws ExceptionExtbaseObject
     */
    public function getSenderEmail(): string
    {
        $defaultSenderEmail = TypoScriptUtility::overwriteValueFromTypoScript(
            '',
            $this->configuration['receiver.']['default.'],
            'senderEmail'
        );
        $senderEmail = $this->mailRepository->getSenderMailFromArguments($this->mail, $defaultSenderEmail);

        /** @var ReceiverMailSenderPropertiesGetSenderEmailEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ReceiverMailSenderPropertiesGetSenderEmailEvent($senderEmail, $this)
        );
        return $event->getSenderEmail();
    }

    /**
     * Get sender name from configuration in fields and params. If empty, take default from TypoScript
     *
     * @throws ExceptionExtbaseObject
     */
    public function getSenderName(): string
    {
        $defaultSenderName = TypoScriptUtility::overwriteValueFromTypoScript(
            '',
            $this->configuration['receiver.']['default.'],
            'senderName'
        );
        $senderName = $this->mailRepository->getSenderNameFromArguments($this->mail, $defaultSenderName);

        /** @var ReceiverMailSenderPropertiesGetSenderNameEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ReceiverMailSenderPropertiesGetSenderNameEvent($senderName, $this)
        );
        return $event->getSenderName();
    }
}
