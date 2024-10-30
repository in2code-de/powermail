<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\SenderMailPropertiesService;

final class SenderMailPropertiesGetSenderEmailEvent
{
    public function __construct(protected string $senderEmail, protected SenderMailPropertiesService $senderMailPropertiesService)
    {
    }

    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    public function setSenderEmail(string $senderEmail): SenderMailPropertiesGetSenderEmailEvent
    {
        $this->senderEmail = $senderEmail;
        return $this;
    }

    public function getSenderMailPropertiesService(): SenderMailPropertiesService
    {
        return $this->senderMailPropertiesService;
    }
}
