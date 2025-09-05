<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\ReceiverMailSenderPropertiesService;

final class ReceiverMailSenderPropertiesGetSenderNameEvent
{
    public function __construct(protected string $senderName, protected ReceiverMailSenderPropertiesService $service)
    {
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function setSenderName(string $senderName): ReceiverMailSenderPropertiesGetSenderNameEvent
    {
        $this->senderName = $senderName;
        return $this;
    }

    public function getService(): ReceiverMailSenderPropertiesService
    {
        return $this->service;
    }
}
