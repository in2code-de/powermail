<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield;

use In2code\Powermail\Utility\FrontendUtility;

/**
 * Class AbstractMethod
 */
abstract class AbstractMethod implements MethodInterface
{
    protected array $arguments;

    public function __construct(protected \In2code\Powermail\Domain\Model\Mail $mail, protected array $settings, protected array $flexForm, protected array $configuration = [])
    {
        $this->arguments = FrontendUtility::getArguments();
    }

    public function initialize(): void
    {
    }

    public function initializeSpamCheck(): void
    {
    }

    /**
     * Example spamcheck, return true if spam recocnized
     */
    public function spamCheck(): bool
    {
        return false;
    }
}
