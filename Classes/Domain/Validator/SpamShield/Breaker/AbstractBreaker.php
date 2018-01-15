<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield\Breaker;

use In2code\Powermail\Domain\Model\Mail;

/**
 * Class AbstractBreaker
 */
abstract class AbstractBreaker implements BreakerInterface
{

    /**
     * @var Mail
     */
    protected $mail = null;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    protected $flexForm = [];

    /**
     * @param Mail $mail
     * @param array $settings
     * @param array $flexForm
     * @param array $configuration
     */
    public function __construct(Mail $mail, array $settings, array $flexForm, array $configuration = [])
    {
        $this->setMail($mail);
        $this->setSettings($settings);
        $this->setFlexForm($flexForm);
        $this->setConfiguration($configuration);
    }

    /**
     * @return void
     */
    public function initialize()
    {
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
     * @return AbstractBreaker
     */
    public function setMail(Mail $mail)
    {
        $this->mail = $mail;
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
     * @return AbstractBreaker
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
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
     * @return AbstractBreaker
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return array
     */
    public function getFlexForm(): array
    {
        return $this->flexForm;
    }

    /**
     * @param array $flexForm
     * @return AbstractBreaker
     */
    public function setFlexForm(array $flexForm)
    {
        $this->flexForm = $flexForm;
        return $this;
    }
}
