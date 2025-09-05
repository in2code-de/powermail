<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\CustomValidator;

final class CustomValidatorEvent
{
    public function __construct(protected Mail $mail, protected CustomValidator $customValidator)
    {
    }

    public function getMail(): Mail
    {
        return $this->mail;
    }

    public function getCustomValidator(): CustomValidator
    {
        return $this->customValidator;
    }
}
