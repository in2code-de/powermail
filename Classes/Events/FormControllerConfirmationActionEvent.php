<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Mail;

final class FormControllerConfirmationActionEvent
{
    public function __construct(protected Mail $mail, protected FormController $formController)
    {
    }

    public function getMail(): Mail
    {
        return $this->mail;
    }

    public function getFormController(): FormController
    {
        return $this->formController;
    }
}
