<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\SendMailService;
use TYPO3\CMS\Core\Mail\MailMessage;

final class SendMailServicePrepareAndSendEvent
{
    protected bool $allowedToSend = true;

    public function __construct(protected MailMessage $mailMessage, protected array $email, protected SendMailService $sendMailService)
    {
    }

    public function getMailMessage(): MailMessage
    {
        return $this->mailMessage;
    }

    public function getEmail(): array
    {
        return $this->email;
    }

    public function setEmail(array $email): SendMailServicePrepareAndSendEvent
    {
        $this->email = $email;
        return $this;
    }

    public function getSendMailService(): SendMailService
    {
        return $this->sendMailService;
    }

    public function isAllowedToSend(): bool
    {
        return $this->allowedToSend;
    }

    public function setAllowedToSend(bool $allowedToSend): SendMailServicePrepareAndSendEvent
    {
        $this->allowedToSend = $allowedToSend;
        return $this;
    }

    public function setMailMessage(MailMessage $mailMessage): SendMailServicePrepareAndSendEvent
    {
        $this->mailMessage = $mailMessage;
        return $this;
    }
}
