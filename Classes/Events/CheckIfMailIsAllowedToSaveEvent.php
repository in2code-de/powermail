<?php

namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Mail;

final class CheckIfMailIsAllowedToSaveEvent
{
    protected bool $savingOfMailAllowed = true;

    public function __construct(protected Mail $mail)
    {
    }

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
