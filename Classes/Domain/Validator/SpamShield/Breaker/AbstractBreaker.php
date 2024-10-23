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
    protected $mail;

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

    public function __construct(Mail $mail, array $settings, array $flexForm, array $configuration = [])
    {
        $this->setMail($mail);
        $this->setSettings($settings);
        $this->setFlexForm($flexForm);
        $this->setConfiguration($configuration);
    }

    public function initialize(): void
    {
    }

    public function getMail(): Mail
    {
        return $this->mail;
    }

    /**
     * @return AbstractBreaker
     */
    public function setMail(Mail $mail): BreakerInterface
    {
        $this->mail = $mail;
        return $this;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @return AbstractBreaker
     */
    public function setConfiguration(array $configuration): BreakerInterface
    {
        $this->configuration = $configuration;
        return $this;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @return AbstractBreaker
     */
    public function setSettings(array $settings): BreakerInterface
    {
        $this->settings = $settings;
        return $this;
    }

    public function getFlexForm(): array
    {
        return $this->flexForm;
    }

    /**
     * @return AbstractBreaker
     */
    public function setFlexForm(array $flexForm): BreakerInterface
    {
        $this->flexForm = $flexForm;
        return $this;
    }
}
