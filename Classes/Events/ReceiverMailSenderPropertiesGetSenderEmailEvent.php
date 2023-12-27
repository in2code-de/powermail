<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\ReceiverMailSenderPropertiesService;

final class ReceiverMailSenderPropertiesGetSenderEmailEvent
{
    /**
     * @var string
     */
    protected string $senderEmail;

    /**
     * @var ReceiverMailSenderPropertiesService
     */
    protected ReceiverMailSenderPropertiesService $service;

    /**
     * @param string $senderEmail
     * @param ReceiverMailSenderPropertiesService $service
     */
    public function __construct(string $senderEmail, ReceiverMailSenderPropertiesService $service)
    {
        $this->senderEmail = $senderEmail;
        $this->service = $service;
    }

    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    /**
     * @param string $senderEmail
     * @return ReceiverMailSenderPropertiesGetSenderEmailEvent
     */
    public function setSenderEmail(string $senderEmail): ReceiverMailSenderPropertiesGetSenderEmailEvent
    {
        $this->senderEmail = $senderEmail;
        return $this;
    }

    /**
     * @return ReceiverMailSenderPropertiesService
     */
    public function getService(): ReceiverMailSenderPropertiesService
    {
        return $this->service;
    }
}
