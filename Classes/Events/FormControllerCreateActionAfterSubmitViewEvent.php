<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Mail;

final class FormControllerCreateActionAfterSubmitViewEvent
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
     * Constructor
     *
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
     * @return FormControllerCreateActionAfterSubmitViewEvent
     */
    public function setHash(string $hash): FormControllerCreateActionAfterSubmitViewEvent
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return FormController
     */
    public function getFormController(): FormController
    {
        return $this->formController;
    }
}
