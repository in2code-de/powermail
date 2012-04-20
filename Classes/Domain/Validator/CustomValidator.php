<?php
class Tx_Powermail_Domain_Validator_CustomValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * @var Tx_Extbase_SignalSlot_Dispatcher
	 */
	protected $signalSlotDispatcher;

	/**
	 * Return variable
	 *
	 * @var bool
	 */
	public $isValid = true;

	/**
	 * Validation of given fields with a SignalSlot
	 *
	 * @param $params
	 * @return bool
	 */
	public function isValid($params) {
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__, array($params, $this));
		return $this->isValid;
	}

	/**
	 * Function can be called from slot to set an error message
	 *
	 * @param $message
	 * @param $code
	 */
	public function setError($message, $code) {
		$this->addError($message, $code);
	}

	/**
	 * @param Tx_Extbase_SignalSlot_Dispatcher $signalSlotDispatcher
	 */
	public function injectSignalSlotDispatcher(Tx_Extbase_SignalSlot_Dispatcher $signalSlotDispatcher) {
	    $this->signalSlotDispatcher = $signalSlotDispatcher;
	}

}
?>