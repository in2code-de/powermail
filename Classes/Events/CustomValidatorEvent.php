<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\CustomValidator;

final class CustomValidatorEvent
{
    /**
     * @var Mail
     */
    protected Mail $mail;

    /**
     * @var CustomValidator
     */
    protected CustomValidator $customValidator;

    /**
     * @param Mail $mail
     * @param CustomValidator $customValidator
     */
    public function __construct(Mail $mail, CustomValidator $customValidator)
    {
        $this->mail = $mail;
        $this->customValidator = $customValidator;
    }

    /**
     * @return Mail
     */
    public function getMail(): Mail
    {
        return $this->mail;
    }

    /**
     * @return CustomValidator
     */
    public function getCustomValidator(): CustomValidator
    {
        return $this->customValidator;
    }
}
