<?php

declare(strict_types=1);
namespace In2code\Powermail\DataProcessor;

use In2code\Powermail\Domain\Model\Mail;

/**
 * Interface DataProcessorInterface
 */
interface DataProcessorInterface
{
    public function getMail(): Mail;

    public function setMail(Mail $mail): DataProcessorInterface;

    public function getSettings(): array;

    public function setSettings(array $settings): DataProcessorInterface;

    public function getActionMethodName(): string;

    public function setActionMethodName(string $actionMethodName): DataProcessorInterface;

    public function initializeDataProcessor(): void;
}
