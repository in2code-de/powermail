<?php

declare(strict_types=1);
namespace In2code\Powermail\DataProcessor;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class AbstractDataProcessor
 */
abstract class AbstractDataProcessor implements DataProcessorInterface
{
    /**
     * @var Mail
     */
    protected $mail;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Finisher service configuration
     *
     * @var array
     */
    protected $configuration;

    /**
     * Controller actionName - usually "createAction" or "confirmationAction"
     */
    protected $actionMethodName;

    public function getMail(): Mail
    {
        return $this->mail;
    }

    public function setMail(Mail $mail): DataProcessorInterface
    {
        $this->mail = $mail;
        return $this;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setSettings(array $settings): DataProcessorInterface
    {
        $this->settings = $settings;
        return $this;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): DataProcessorInterface
    {
        $this->configuration = $configuration;
        return $this;
    }

    public function getActionMethodName(): string
    {
        return (string)$this->actionMethodName;
    }

    /**
     * @param string $actionMethodName
     */
    public function setActionMethodName($actionMethodName): DataProcessorInterface
    {
        $this->actionMethodName = $actionMethodName;
        return $this;
    }

    public function initializeDataProcessor(): void
    {
    }

    public function __construct(
        Mail $mail,
        array $configuration,
        array $settings,
        string $actionMethodName,
        protected \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObject
    ) {
        $this->setMail($mail);
        $this->setConfiguration($configuration);
        $this->setSettings($settings);
        $this->setActionMethodName($actionMethodName);
    }
}
