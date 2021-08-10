<?php
declare(strict_types = 1);
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
     *
     * @var null
     */
    protected $actionMethodName = null;

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @return Mail
     */
    public function getMail(): Mail
    {
        return $this->mail;
    }

    /**
     * @param Mail $mail
     * @return DataProcessorInterface
     */
    public function setMail(Mail $mail): DataProcessorInterface
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
     * @return DataProcessorInterface
     */
    public function setSettings(array $settings): DataProcessorInterface
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
     * @return DataProcessorInterface
     */
    public function setConfiguration(array $configuration): DataProcessorInterface
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @return string
     */
    public function getActionMethodName(): string
    {
        return (string)$this->actionMethodName;
    }

    /**
     * @param string $actionMethodName
     * @return DataProcessorInterface
     */
    public function setActionMethodName($actionMethodName): DataProcessorInterface
    {
        $this->actionMethodName = $actionMethodName;
        return $this;
    }

    /**
     * @return void
     */
    public function initializeDataProcessor(): void
    {
    }

    /**
     * @param Mail $mail
     * @param array $configuration
     * @param array $settings
     * @param string $actionMethodName
     * @param ContentObjectRenderer $contentObject
     */
    public function __construct(
        Mail $mail,
        array $configuration,
        array $settings,
        string $actionMethodName,
        ContentObjectRenderer $contentObject
    ) {
        $this->setMail($mail);
        $this->setConfiguration($configuration);
        $this->setSettings($settings);
        $this->setActionMethodName($actionMethodName);
        $this->contentObject = $contentObject;
    }
}
