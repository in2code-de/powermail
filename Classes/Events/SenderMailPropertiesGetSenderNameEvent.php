<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\SenderMailPropertiesService;

final class SenderMailPropertiesGetSenderNameEvent
{
    /**
     * @var string
     */
    protected string $senderName;

    /**
     * @var SenderMailPropertiesService
     */
    protected SenderMailPropertiesService $senderMailPropertiesService;

    /**
     * @param string $senderName
     * @param SenderMailPropertiesService $senderMailPropertiesService
     */
    public function __construct(string $senderName, SenderMailPropertiesService $senderMailPropertiesService)
    {
        $this->senderName = $senderName;
        $this->senderMailPropertiesService = $senderMailPropertiesService;
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
     * @return SenderMailPropertiesGetSenderNameEvent
     */
    public function setSenderName(string $senderName): SenderMailPropertiesGetSenderNameEvent
    {
        $this->senderName = $senderName;
        return $this;
    }

    /**
     * @return SenderMailPropertiesService
     */
    public function getSenderMailPropertiesService(): SenderMailPropertiesService
    {
        return $this->senderMailPropertiesService;
    }
}
