<?php

declare(strict_types=1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;

/**
 * Interface FinisherInterface
 */
interface FinisherInterface
{
    public function getMail(): Mail;

    public function setMail(Mail $mail): FinisherInterface;

    public function getSettings(): array;

    public function setSettings(array $settings): FinisherInterface;

    public function isFormSubmitted(): bool;

    public function setFormSubmitted(bool $formSubmitted): FinisherInterface;

    public function getActionMethodName(): string;

    public function setActionMethodName(string $actionMethodName): FinisherInterface;

    public function initializeFinisher(): void;
}
