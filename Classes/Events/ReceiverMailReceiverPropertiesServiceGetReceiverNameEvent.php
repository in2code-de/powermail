<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService;

final class ReceiverMailReceiverPropertiesServiceGetReceiverNameEvent
{
    public function __construct(protected string $receiverName, protected ReceiverMailReceiverPropertiesService $service)
    {
    }

    public function getReceiverName(): string
    {
        return $this->receiverName;
    }

    public function setReceiverName(string $receiverName): ReceiverMailReceiverPropertiesServiceGetReceiverNameEvent
    {
        $this->receiverName = $receiverName;
        return $this;
    }

    public function getService(): ReceiverMailReceiverPropertiesService
    {
        return $this->service;
    }
}
