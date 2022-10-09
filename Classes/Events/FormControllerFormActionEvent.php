<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Form;

final class FormControllerFormActionEvent
{
    /**
     * @var Form|null
     */
    protected ?Form $form;

    /**
     * @var FormController
     */
    protected FormController $formController;

    /**
     * @param Form|null $form
     * @param FormController $formController
     */
    public function __construct(?Form $form, FormController $formController)
    {
        $this->form = $form;
        $this->formController = $formController;
    }

    /**
     * @return Form|null
     */
    public function getForm(): ?Form
    {
        return $this->form;
    }

    /**
     * @param Form|null $form
     * @return FormControllerFormActionEvent
     */
    public function setForm(?Form $form): FormControllerFormActionEvent
    {
        $this->form = $form;
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
