<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService;

final class ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent
{
    public function __construct(protected array $emailArray, protected ReceiverMailReceiverPropertiesService $service)
    {
    }

    public function getEmailArray(): array
    {
        return $this->emailArray;
    }

    public function setEmailArray(array $emailArray): ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent
    {
        $this->emailArray = $emailArray;
        return $this;
    }

    public function getService(): ReceiverMailReceiverPropertiesService
    {
        return $this->service;
    }
}
