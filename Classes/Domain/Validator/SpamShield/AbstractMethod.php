<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractMethod
 */
abstract class AbstractMethod implements MethodInterface
{

    /**
     * @var null|Mail
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
        $this->arguments = GeneralUtility::_GP('tx_powermail_pi1');
    }

    /**
     * @return void
     */
    public function initialize()
    {
    }

    /**
     * @return void
     */
    public function initializeSpamCheck()
    {
    }

    /**
     * Example spamcheck, return true if spam recocnized
     *
     * @return bool
     */
    public function spamCheck()
    {
        return false;
    }
}
