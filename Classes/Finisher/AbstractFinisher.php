<?php

declare(strict_types=1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class AbstractFinisher
 */
abstract class AbstractFinisher implements FinisherInterface
{
    protected Mail $mail;

    /**
     * Extension settings
     */
    protected array $settings = [];

    /**
     * Finisher service configuration
     */
    protected array $configuration = [];

    /**
     * Was form finally submitted?
     */
    protected bool $formSubmitted = false;

    /**
     * Controller actionName - usually "createAction" or "confirmationAction"
     */
    protected string $actionMethodName = '';

    public function __construct(
        Mail $mail,
        array $configuration,
        array $settings,
        bool $formSubmitted,
        string $actionMethodName,
        protected ContentObjectRenderer $contentObject
    ) {
        $this->setMail($mail);
        $this->setConfiguration($configuration);
        $this->setSettings($settings);
        $this->setFormSubmitted($formSubmitted);
        $this->setActionMethodName($actionMethodName);
    }

    public function getMail(): Mail
    {
        return $this->mail;
    }

    public function setMail(Mail $mail): FinisherInterface
    {
        $this->mail = $mail;
        return $this;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setSettings(array $settings): FinisherInterface
    {
        $this->settings = $settings;
        return $this;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): FinisherInterface
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Form is not marked as submitted in case of optin usage
     */
    public function isFormSubmitted(): bool
    {
        return $this->formSubmitted;
    }

    public function setFormSubmitted(bool $formSubmitted): FinisherInterface
    {
        $this->formSubmitted = $formSubmitted;
        return $this;
    }

    public function getActionMethodName(): string
    {
        return $this->actionMethodName;
    }

    public function setActionMethodName(string $actionMethodName): FinisherInterface
    {
        $this->actionMethodName = $actionMethodName;
        return $this;
    }

    public function initializeFinisher(): void
    {
    }
}
