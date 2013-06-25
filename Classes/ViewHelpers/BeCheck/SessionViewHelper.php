<?php

/**
 * Backend Check Viewhelper: Check if Session works correct on the server
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_BeCheck_SessionViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * Session Key
	 *
	 * @var string
	 */
	public $sessionKey = 'powermail_be_check_test';

	/**
	 * Check FE Session
	 *
	 * @return 	boolean
	 */
	public function render() {
		// settings
		global $TYPO3_CONF_VARS;
		$userObj = tslib_eidtools::initFeUser();
		$GLOBALS['TSFE'] = t3lib_div::makeInstance(
			'tslib_fe',
			$TYPO3_CONF_VARS,
			t3lib_div::_GET('id'),
			0,
			true
		);
		$GLOBALS['TSFE']->fe_user = $userObj;

		// random value for session storing
		$value = md5(time());

		// store in session
		$GLOBALS['TSFE']->fe_user->setKey('ses', $this->sessionKey, $value);
		$GLOBALS['TSFE']->storeSessionData();

		if ($GLOBALS['TSFE']->fe_user->getKey('ses', $this->sessionKey) === $value) {
			return true;
		}
		return false;
	}
}

?>