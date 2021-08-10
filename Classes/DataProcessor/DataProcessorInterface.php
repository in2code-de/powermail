<?php
declare(strict_types = 1);
namespace In2code\Powermail\DataProcessor;

use In2code\Powermail\Domain\Model\Mail;

/**
 * Interface DataProcessorInterface
 */
interface DataProcessorInterface
{

    /**
     * @return Mail
     */
    public function getMail(): Mail;

    /**
     * @param Mail $mail
     * @return DataProcessorInterface
     */
    public function setMail(Mail $mail): DataProcessorInterface;

    /**
     * @return array
     */
    public function getSettings(): array;

    /**
     * @param array $settings
     * @return DataProcessorInterface
     */
    public function setSettings(array $settings): DataProcessorInterface;

    /**
     * @return string
     */
    public function getActionMethodName(): string;

    /**
     * @param string $actionMethodName
     * @return DataProcessorInterface
     */
    public function setActionMethodName(string $actionMethodName): DataProcessorInterface;

    /**
     * @return void
     */
    public function initializeDataProcessor(): void;
}
