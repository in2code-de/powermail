<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Mail;
use TYPO3Fluid\Fluid\View\ViewInterface;

final class FormControllerOptinConfirmActionBeforeRenderViewEvent
{
    /**
     * @var Mail
     */
    protected Mail $mail;

    /**
     * @var ViewInterface|null
     */
    protected ?ViewInterface $view;
    
    /**
     * @var string
     */
    protected string $hash;

    /**
     * @var FormController
     */
    protected FormController $formController;

    /**
     * @param Mail $mail
     * @param string $hash
     * @param FormController $formController
     * @param ViewInterface|null $view
     */
    public function __construct(Mail $mail, string $hash, FormController $formController, ?ViewInterface $view=null)
    {
        $this->mail = $mail;
        $this->hash = $hash;
        $this->formController = $formController;
        $this->view = $view;
    }

    /**
     * @return Mail
     */
    public function getMail(): Mail
    {
        return $this->mail;
    }

    /**
     * @param Mail $mail
     */
    public function setMail(Mail $mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return ViewInterface|null
     */
    public function getView(): ?ViewInterface
    {
        return $this->view;
    }

    /**
     * @param ViewInterface|null $view
     */
    public function setView(?ViewInterface $view): void
    {
        $this->view = $view;
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
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return FormController
     */
    public function getFormController(): FormController
    {
        return $this->formController;
    }
}
