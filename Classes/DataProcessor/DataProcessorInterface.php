<?php
declare(strict_types=1);
namespace In2code\Powermail\DataProcessor;

use In2code\Powermail\Domain\Model\Mail;

/**
 * Interface DataProcessorInterface
 */
interface DataProcessorInterface
{

    /**
     * Get mail
     *
     * @return Mail
     */
    public function getMail();

    /**
     * Set mail
     *
     * @param Mail $mail
     * @return AbstractDataProcessor
     */
    public function setMail($mail);

    /**
     * Get settings
     *
     * @return array
     */
    public function getSettings();

    /**
     * Set settings
     *
     * @param array $settings
     * @return AbstractDataProcessor
     */
    public function setSettings($settings);

    /**
     * @return null
     */
    public function getActionMethodName();

    /**
     * @param null $actionMethodName
     * @return AbstractDataProcessor
     */
    public function setActionMethodName($actionMethodName);

    /**
     * @return void
     */
    public function initializeDataProcessor();
}
