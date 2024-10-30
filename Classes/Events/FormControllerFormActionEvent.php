<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Form;

final class FormControllerFormActionEvent
{
    public function __construct(protected ?Form $form, protected FormController $formController)
    {
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form): FormControllerFormActionEvent
    {
        $this->form = $form;
        return $this;
    }

    public function getFormController(): FormController
    {
        return $this->formController;
    }
}
