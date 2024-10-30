<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Mail;

/**
 * Interface MethodInterface
 */
interface MethodInterface
{
    public function __construct(Mail $mail, array $settings, array $flexForm, array $configuration = []);

    public function initialize(): void;

    public function initializeSpamCheck(): void;

    public function spamCheck(): bool;
}
