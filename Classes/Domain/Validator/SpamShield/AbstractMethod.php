<?php
namespace In2code\Powermail\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractMethod
 * @package In2code\Powermail\Domain\Validator\SpamShield
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
     * @param Mail $mail
     * @param array $settings
     * @param array $configuration
     */
    public function __construct(Mail $mail, array $settings, array $configuration = [])
    {
        $this->mail = $mail;
        $this->settings = $settings;
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
     * Example spamcheck
     *
     * @param int $float
     * @return int
     */
    public function spamCheck($float = 0)
    {
        return $float;
    }
}
