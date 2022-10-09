<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;

final class FormControllerOptinConfirmActionBeforeRenderViewEvent
{
    /**
     * @var int
     */
    protected int $mailIdentifier;

    /**
     * @var string
     */
    protected string $hash;

    /**
     * @var FormController
     */
    protected FormController $formController;

    /**
     * @param int $mailIdentifier
     * @param string $hash
     * @param FormController $formController
     */
    public function __construct(int $mailIdentifier, string $hash, FormController $formController)
    {
        $this->mailIdentifier = $mailIdentifier;
        $this->hash = $hash;
        $this->formController = $formController;
    }

    /**
     * @return int
     */
    public function getMailIdentifier(): int
    {
        return $this->mailIdentifier;
    }

    /**
     * @param int $mailIdentifier
     * @return FormControllerOptinConfirmActionBeforeRenderViewEvent
     */
    public function setMailIdentifier(int $mailIdentifier): FormControllerOptinConfirmActionBeforeRenderViewEvent
    {
        $this->mailIdentifier = $mailIdentifier;
        return $this;
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
     * @return FormControllerOptinConfirmActionBeforeRenderViewEvent
     */
    public function setHash(string $hash): FormControllerOptinConfirmActionBeforeRenderViewEvent
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
