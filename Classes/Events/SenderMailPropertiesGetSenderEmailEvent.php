<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\SenderMailPropertiesService;

final class SenderMailPropertiesGetSenderEmailEvent
{
    /**
     * @var string
     */
    protected string $senderEmail;

    /**
     * @var SenderMailPropertiesService
     */
    protected SenderMailPropertiesService $senderMailPropertiesService;

    /**
     * @param string $senderEmail
     * @param SenderMailPropertiesService $senderMailPropertiesService
     */
    public function __construct(string $senderEmail, SenderMailPropertiesService $senderMailPropertiesService)
    {
        $this->senderEmail = $senderEmail;
        $this->senderMailPropertiesService = $senderMailPropertiesService;
    }

    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    /**
     * @param string $senderEmail
     * @return SenderMailPropertiesGetSenderEmailEvent
     */
    public function setSenderEmail(string $senderEmail): SenderMailPropertiesGetSenderEmailEvent
    {
        $this->senderEmail = $senderEmail;
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
