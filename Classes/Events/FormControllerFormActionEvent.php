<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Form;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3Fluid\Fluid\View\ViewInterface;

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

    public function getView(): ?ViewInterface
    {
        return $this->getFormController()->getView();
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->getFormController()->getRequest();
    }
}
