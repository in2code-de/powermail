<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\ReceiverMailSenderPropertiesService;

final class ReceiverMailSenderPropertiesGetSenderEmailEvent
{
    public function __construct(protected string $senderEmail, protected ReceiverMailSenderPropertiesService $service)
    {
    }

    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    public function setSenderEmail(string $senderEmail): ReceiverMailSenderPropertiesGetSenderEmailEvent
    {
        $this->senderEmail = $senderEmail;
        return $this;
    }

    public function getService(): ReceiverMailSenderPropertiesService
    {
        return $this->service;
    }
}
