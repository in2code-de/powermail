<?php
declare(strict_types = 1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class AbstractFinisher
 */
abstract class AbstractFinisher implements FinisherInterface
{
    /**
     * @var Mail
     */
    protected $mail;

    /**
     * Extension settings
     *
     * @var array
     */
    protected $settings = [];

    /**
     * Finisher service configuration
     *
     * @var array
     */
    protected $configuration = [];

    /**
     * Was form finally submitted?
     *
     * @var bool
     */
    protected $formSubmitted = false;

    /**
     * Controller actionName - usually "createAction" or "confirmationAction"
     *
     * @var string
     */
    protected $actionMethodName = '';

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @param Mail $mail
     * @param array $configuration
     * @param array $settings
     * @param bool $formSubmitted
     * @param string $actionMethodName
     * @param ContentObjectRenderer $contentObject
     */
    public function __construct(
        Mail $mail,
        array $configuration,
        array $settings,
        bool $formSubmitted,
        string $actionMethodName,
        ContentObjectRenderer $contentObject
    ) {
        $this->setMail($mail);
        $this->setConfiguration($configuration);
        $this->setSettings($settings);
        $this->setFormSubmitted($formSubmitted);
        $this->setActionMethodName($actionMethodName);
        $this->contentObject = $contentObject;
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
     * @return FinisherInterface
     */
    public function setMail(Mail $mail): FinisherInterface
    {
        $this->mail = $mail;
        return $this;
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
     * @return FinisherInterface
     */
    public function setSettings(array $settings): FinisherInterface
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     * @return FinisherInterface
     */
    public function setConfiguration(array $configuration): FinisherInterface
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Form is not marked as submitted in case of optin usage
     *
     * @return bool
     */
    public function isFormSubmitted(): bool
    {
        return $this->formSubmitted;
    }

    /**
     * @param bool $formSubmitted
     * @return FinisherInterface
     */
    public function setFormSubmitted(bool $formSubmitted): FinisherInterface
    {
        $this->formSubmitted = $formSubmitted;
        return $this;
    }

    /**
     * @return string
     */
    public function getActionMethodName(): string
    {
        return $this->actionMethodName;
    }

    /**
     * @param string $actionMethodName
     * @return FinisherInterface
     */
    public function setActionMethodName(string $actionMethodName): FinisherInterface
    {
        $this->actionMethodName = $actionMethodName;
        return $this;
    }

    /**
     * @return void
     */
    public function initializeFinisher(): void
    {
    }
}
