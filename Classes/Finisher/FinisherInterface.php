<?php
declare(strict_types = 1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;

/**
 * Interface FinisherInterface
 */
interface FinisherInterface
{

    /**
     * @return Mail
     */
    public function getMail(): Mail;

    /**
     * @param Mail $mail
     * @return FinisherInterface
     */
    public function setMail(Mail $mail): FinisherInterface;

    /**
     * @return array
     */
    public function getSettings(): array;

    /**
     * @param array $settings
     * @return FinisherInterface
     */
    public function setSettings(array $settings): FinisherInterface;

    /**
     * @return bool
     */
    public function isFormSubmitted(): bool;

    /**
     * @param bool $formSubmitted
     * @return FinisherInterface
     */
    public function setFormSubmitted(bool $formSubmitted): FinisherInterface;

    /**
     * @return string
     */
    public function getActionMethodName(): string;

    /**
     * @param string $actionMethodName
     * @return FinisherInterface
     */
    public function setActionMethodName(string $actionMethodName): FinisherInterface;

    /**
     * @return void
     */
    public function initializeFinisher(): void;
}
