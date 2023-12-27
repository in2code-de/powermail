<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Mail;

final class FormControllerConfirmationActionEvent
{
    /**
     * @var Mail
     */
    protected Mail $mail;

    /**
     * @var FormController
     */
    protected FormController $formController;

    /**
     * @param Mail $mail
     * @param FormController $formController
     */
    public function __construct(Mail $mail, FormController $formController)
    {
        $this->mail = $mail;
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
     * @return FormController
     */
    public function getFormController(): FormController
    {
        return $this->formController;
    }
}
