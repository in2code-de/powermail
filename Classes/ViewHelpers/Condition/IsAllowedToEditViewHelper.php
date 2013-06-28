<?php

/**
 * Check if logged in User is allowed to edit
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Condition_IsAllowedToEditViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * Div Methods
	 *
	 * @var		Tx_Powermail_Utility_Div
	 */
	protected $div;

	/**
	 * Check if logged in User is allowed to edit
	 *
	 * @param 	array 		TypoScript and FlexForm Settings
	 * @param 	object 		Mail Object
	 * @return 	boolean
	 */
	public function render($settings = array(), $mail) {
		if ($this->div->isAllowedToEdit($settings, $mail)) {
			return true;
		}
		return false;
    }

	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct() {
		$this->div = t3lib_div::makeInstance('Tx_Powermail_Utility_Div');
	}
}

?>