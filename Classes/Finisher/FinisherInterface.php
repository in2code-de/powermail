<?php
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;

/**
 * Interface FinisherInterface
 *
 * @package In2code\Powermail\Finisher
 */
interface FinisherInterface {

	/**
	 * Get mail
	 *
	 * @return Mail
	 */
	public function getMail();

	/**
	 * Set mail
	 *
	 * @params Mail $mail
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
	 * @params array $settings
	 * @return AbstractFinisher
	 */
	public function setSettings($settings);

	/**
	 * @return void
	 */
	public function initializeFinisher();
}
