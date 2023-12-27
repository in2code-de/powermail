<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;

final class FormControllerInitializeObjectEvent
{
    /**
     * @var array
     */
    protected array $settings;

    /**
     * @var FormController
     */
    protected FormController $formController;

    /**
     * @param array $settings
     * @param FormController $formController
     */
    public function __construct(array $settings, FormController $formController)
    {
        $this->settings = $settings;
        $this->formController = $formController;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return FormControllerInitializeObjectEvent
     */
    public function setSettings(array $settings): FormControllerInitializeObjectEvent
    {
        $this->settings = $settings;
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
