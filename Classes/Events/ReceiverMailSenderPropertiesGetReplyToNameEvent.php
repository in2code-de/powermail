<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\ReceiverMailSenderPropertiesService;

final class ReceiverMailSenderPropertiesGetReplyToNameEvent
{
    public function __construct(protected string $senderName, protected ReceiverMailSenderPropertiesService $service)
    {
    }

    public function getReplyToName(): string
    {
        return $this->senderName;
    }

    public function setReplyToName(string $senderName): ReceiverMailSenderPropertiesGetReplyToNameEvent
    {
        $this->senderName = $senderName;
        return $this;
    }

    public function getService(): ReceiverMailSenderPropertiesService
    {
        return $this->service;
    }
}
