<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Mail;

final class FormControllerOptinConfirmActionAfterPersistEvent
{
    /**
     * Constructor
     */
    public function __construct(protected Mail $mail, protected string $hash, protected FormController $formController)
    {
    }

    public function setMail(Mail $mail): void
    {
        $this->mail = $mail;
    }

    public function getMail(): Mail
    {
        return $this->mail;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getFormController(): FormController
    {
        return $this->formController;
    }
}
