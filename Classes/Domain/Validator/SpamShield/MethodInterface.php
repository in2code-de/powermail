<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Mail;

/**
 * Interface MethodInterface
 */
interface MethodInterface
{

    /**
     * @param Mail $mail
     * @param array $settings
     * @param array $flexForm
     * @param array $configuration
     */
    public function __construct(Mail $mail, array $settings, array $flexForm, array $configuration = []);

    /**
     * @return void
     */
    public function initialize();

    /**
     * @return void
     */
    public function initializeSpamCheck();

    /**
     * @return bool
     */
    public function spamCheck();
}
