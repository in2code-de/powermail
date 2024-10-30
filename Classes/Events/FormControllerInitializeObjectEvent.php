<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;

final class FormControllerInitializeObjectEvent
{
    public function __construct(protected array $settings, protected FormController $formController)
    {
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setSettings(array $settings): FormControllerInitializeObjectEvent
    {
        $this->settings = $settings;
        return $this;
    }

    public function getFormController(): FormController
    {
        return $this->formController;
    }
}
