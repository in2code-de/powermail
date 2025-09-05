<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Form;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3Fluid\Fluid\View\ViewInterface as FluidViewInterface;

final class FormControllerFormActionEvent
{
    /**
     * @var array<string,mixed>
     */
    protected array $viewVariables = [];

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

    public function getView(): ViewInterface|FluidViewInterface
    {
        return $this->getFormController()->getView();
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->getFormController()->getRequest();
    }

    /**
     * @return array<string,mixed>
     */
    public function getViewVariables(): array
    {
        return $this->viewVariables;
    }

    /**
     * Add additional variables to the view
     *
     * @param array<string,mixed> $variables
     * @return void
     */
    public function addViewVariables(array $variables): void
    {
        $this->viewVariables = array_merge($this->viewVariables, $variables);
    }
}
