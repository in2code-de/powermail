<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\ReceiverMailSenderPropertiesService;

final class ReceiverMailSenderPropertiesGetSenderNameEvent
{
    /**
     * @var string
     */
    protected string $senderName;

    /**
     * @var ReceiverMailSenderPropertiesService
     */
    protected ReceiverMailSenderPropertiesService $service;

    /**
     * @param string $senderName
     * @param ReceiverMailSenderPropertiesService $service
     */
    public function __construct(string $senderName, ReceiverMailSenderPropertiesService $service)
    {
        $this->senderName = $senderName;
        $this->service = $service;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     * @return ReceiverMailSenderPropertiesGetSenderNameEvent
     */
    public function setSenderName(string $senderName): ReceiverMailSenderPropertiesGetSenderNameEvent
    {
        $this->senderName = $senderName;
        return $this;
    }

    /**
     * @return ReceiverMailSenderPropertiesService
     */
    public function getService(): ReceiverMailSenderPropertiesService
    {
        return $this->service;
    }
}
