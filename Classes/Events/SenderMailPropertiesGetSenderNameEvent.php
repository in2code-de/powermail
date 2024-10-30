<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\SenderMailPropertiesService;

final class SenderMailPropertiesGetSenderNameEvent
{
    public function __construct(protected string $senderName, protected SenderMailPropertiesService $senderMailPropertiesService)
    {
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function setSenderName(string $senderName): SenderMailPropertiesGetSenderNameEvent
    {
        $this->senderName = $senderName;
        return $this;
    }

    public function getSenderMailPropertiesService(): SenderMailPropertiesService
    {
        return $this->senderMailPropertiesService;
    }
}
