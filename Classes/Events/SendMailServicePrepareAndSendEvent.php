<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\SendMailService;
use TYPO3\CMS\Core\Mail\MailMessage;

final class SendMailServicePrepareAndSendEvent
{
    /**
     * @var MailMessage
     */
    protected MailMessage $mailMessage;

    /**
     * @var array
     */
    protected array $email;

    /**
     * @var SendMailService
     */
    protected SendMailService $sendMailService;

    /**
     * @var bool
     */
    protected bool $allowedToSend = true;

    /**
     * @param MailMessage $mailMessage
     * @param array $email
     * @param SendMailService $sendMailService
     */
    public function __construct(MailMessage $mailMessage, array $email, SendMailService $sendMailService)
    {
        $this->mailMessage = $mailMessage;
        $this->email = $email;
        $this->sendMailService = $sendMailService;
    }

    /**
     * @return MailMessage
     */
    public function getMailMessage(): MailMessage
    {
        return $this->mailMessage;
    }

    /**
     * @return array
     */
    public function getEmail(): array
    {
        return $this->email;
    }

    /**
     * @param array $email
     * @return SendMailServicePrepareAndSendEvent
     */
    public function setEmail(array $email): SendMailServicePrepareAndSendEvent
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return SendMailService
     */
    public function getSendMailService(): SendMailService
    {
        return $this->sendMailService;
    }

    /**
     * @return bool
     */
    public function isAllowedToSend(): bool
    {
        return $this->allowedToSend;
    }

    /**
     * @param bool $allowedToSend
     * @return SendMailServicePrepareAndSendEvent
     */
    public function setAllowedToSend(bool $allowedToSend): SendMailServicePrepareAndSendEvent
    {
        $this->allowedToSend = $allowedToSend;
        return $this;
    }
}
