<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Form;
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

    protected ViewInterface $view;

    /**
     * @param Form|null $form
     * @param FormController $formController
     */
    public function __construct(?Form $form, FormController $formController, ViewInterface $view)
    {
        $this->form = $form;
        $this->formController = $formController;
        $this->view = $view;
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

    /**
     * Add a variable to the view data collection.
     * Can be chained, so $this->view->assign(..., ...)->assign(..., ...); is possible
     *
     * @param string $key Key of variable
     * @param mixed $value Value of object
     * @return ViewInterface an instance of $this, to enable chaining
     */
    public function assign(string $key, mixed $value)
    {
        $this->view->assign($key, $value)
    }

    /**
     * Add multiple variables to the view data collection
     *
     * @param array $values array in the format array(key1 => value1, key2 => value2)
     * @return ViewInterface an instance of $this, to enable chaining
     */
    public function assignMultiple(array $values)
    {
        $this->view->assignMultiple($values);
    }
}
