<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\ReceiverMailSenderPropertiesService;

final class ReceiverMailSenderPropertiesGetReplyToEmailEvent
{
    public function __construct(protected string $senderEmail, protected ReceiverMailSenderPropertiesService $service)
    {
    }

    public function getReplyToEmail(): string
    {
        return $this->senderEmail;
    }

    public function setReplyToEmail(string $senderEmail): ReceiverMailSenderPropertiesGetReplyToEmailEvent
    {
        $this->senderEmail = $senderEmail;
        return $this;
    }

    public function getService(): ReceiverMailSenderPropertiesService
    {
        return $this->service;
    }
}
