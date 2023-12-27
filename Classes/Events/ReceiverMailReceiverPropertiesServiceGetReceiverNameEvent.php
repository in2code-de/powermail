<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService;

final class ReceiverMailReceiverPropertiesServiceGetReceiverNameEvent
{
    /**
     * @var string
     */
    protected string $receiverName;

    /**
     * @var ReceiverMailReceiverPropertiesService
     */
    protected ReceiverMailReceiverPropertiesService $service;

    /**
     * @param string $receiverName
     * @param ReceiverMailReceiverPropertiesService $service
     */
    public function __construct(string $receiverName, ReceiverMailReceiverPropertiesService $service)
    {
        $this->receiverName = $receiverName;
        $this->service = $service;
    }

    /**
     * @return string
     */
    public function getReceiverName(): string
    {
        return $this->receiverName;
    }

    /**
     * @param string $receiverName
     * @return ReceiverMailReceiverPropertiesServiceGetReceiverNameEvent
     */
    public function setReceiverName(string $receiverName): ReceiverMailReceiverPropertiesServiceGetReceiverNameEvent
    {
        $this->receiverName = $receiverName;
        return $this;
    }

    /**
     * @return ReceiverMailReceiverPropertiesService
     */
    public function getService(): ReceiverMailReceiverPropertiesService
    {
        return $this->service;
    }
}
