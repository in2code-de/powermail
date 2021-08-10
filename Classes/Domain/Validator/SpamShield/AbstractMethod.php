<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\FrontendUtility;

/**
 * Class AbstractMethod
 */
abstract class AbstractMethod implements MethodInterface
{

    /**
     * @var Mail|null
     */
    protected $mail = null;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var array
     */
    protected $arguments = [];

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
        $this->mail = $mail;
        $this->settings = $settings;
        $this->flexForm = $flexForm;
        $this->configuration = $configuration;
        $this->arguments = FrontendUtility::getArguments();
    }

    /**
     * @return void
     */
    public function initialize(): void
    {
    }

    /**
     * @return void
     */
    public function initializeSpamCheck(): void
    {
    }

    /**
     * Example spamcheck, return true if spam recocnized
     *
     * @return bool
     */
    public function spamCheck(): bool
    {
        return false;
    }
}
