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

    /**
     * @var Mail
     */
    protected $mail;

    /**
     * Extension settings
     *
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
     * Was form finally submitted?
     *
     * @var bool
     */
    protected $formSubmitted = false;

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
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param Mail $mail
     * @return AbstractFinisher
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return AbstractFinisher
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     * @return AbstractFinisher
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Form is not marked as submitted in case of optin usage
     *
     * @return boolean
     */
    public function isFormSubmitted()
    {
        return $this->formSubmitted;
    }

    /**
     * @param boolean $formSubmitted
     * @return AbstractFinisher
     */
    public function setFormSubmitted($formSubmitted)
    {
        $this->formSubmitted = $formSubmitted;
        return $this;
    }

    /**
     * @return null
     */
    public function getActionMethodName()
    {
        return $this->actionMethodName;
    }

    /**
     * @param null $actionMethodName
     * @return AbstractFinisher
     */
    public function setActionMethodName($actionMethodName)
    {
        $this->actionMethodName = $actionMethodName;
        return $this;
    }

    /**
     * @return void
     */
    public function initializeFinisher()
    {
    }

    /**
     * @param Mail $mail
     * @param array $configuration
     * @param array $settings
     * @param bool $formSubmitted
     * @param ContentObjectRenderer $contentObject
     * @param string $actionMethodName
     */
    public function __construct(
        Mail $mail,
        array $configuration,
        array $settings,
        $formSubmitted,
        $actionMethodName,
        ContentObjectRenderer $contentObject
    ) {
        $this->setMail($mail);
        $this->setConfiguration($configuration);
        $this->setSettings($settings);
        $this->setFormSubmitted($formSubmitted);
        $this->setActionMethodName($actionMethodName);
        $this->contentObject = $contentObject;
    }
}
