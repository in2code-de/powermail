<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Mail;

final class FormControllerDisclaimerActionBeforeRenderViewEvent
{
    /**
     * @var Mail
     */
    protected Mail $mail;

    /**
     * @var string
     */
    protected string $hash;

    /**
     * @var FormController
     */
    protected FormController $formController;

    /**
     * @param Mail $mail
     * @param string $hash
     * @param FormController $formController
     */
    public function __construct(Mail $mail, string $hash, FormController $formController)
    {
        $this->mail = $mail;
        $this->hash = $hash;
        $this->formController = $formController;
    }

    public function setMail(Mail $mail): void
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

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return FormController
     */
    public function getFormController(): FormController
    {
        return $this->formController;
    }
}
