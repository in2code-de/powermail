<?php

namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Mail;

final class CheckIfMailIsAllowedToSaveEvent
{
    /**
     * @var Mail
     */
    protected Mail $mail;

    /**
     * @var bool
     */
    protected bool $savingOfMailAllowed = true;

    /**
     * @param Mail $mail
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return Mail
     */
    public function getMail(): Mail
    {
        return $this->mail;
    }

    public function isSavingOfMailAllowed(): bool
    {
        return $this->savingOfMailAllowed;
    }

    public function setSavingOfMailAllowed(bool $savingOfMailAllowed): void
    {
        $this->savingOfMailAllowed = $savingOfMailAllowed;
    }
}
