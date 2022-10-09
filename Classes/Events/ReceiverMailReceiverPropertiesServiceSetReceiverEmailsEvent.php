<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService;

final class ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent
{
    /**
     * @var array
     */
    protected array $emailArray;

    /**
     * @var ReceiverMailReceiverPropertiesService
     */
    protected ReceiverMailReceiverPropertiesService $service;

    /**
     * @param array $emailArray
     * @param ReceiverMailReceiverPropertiesService $service
     */
    public function __construct(array $emailArray, ReceiverMailReceiverPropertiesService $service)
    {
        $this->emailArray = $emailArray;
        $this->service = $service;
    }

    /**
     * @return array
     */
    public function getEmailArray(): array
    {
        return $this->emailArray;
    }

    /**
     * @param array $emailArray
     * @return ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent
     */
    public function setEmailArray(array $emailArray): ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent
    {
        $this->emailArray = $emailArray;
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
