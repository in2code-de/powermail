<?php
declare(strict_types=1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;

/**
 * Interface FinisherInterface
 */
interface FinisherInterface
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
     * @return AbstractFinisher
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
     * @return AbstractFinisher
     */
    public function setSettings($settings);

    /**
     * @return boolean
     */
    public function isFormSubmitted();

    /**
     * @param boolean $formSubmitted
     * @return AbstractFinisher
     */
    public function setFormSubmitted($formSubmitted);

    /**
     * @return null
     */
    public function getActionMethodName();

    /**
     * @param null $actionMethodName
     * @return AbstractFinisher
     */
    public function setActionMethodName($actionMethodName);

    /**
     * @return void
     */
    public function initializeFinisher();
}
