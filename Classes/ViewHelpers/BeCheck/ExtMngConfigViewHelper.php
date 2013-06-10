<?php

/**
 * Backend Check Viewhelper: Check if Extension Manager Settings are available
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_BeCheck_ExtMngConfigViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * Check if Extension Manager Settings are available
     *
     * @return 	boolean
     */
    public function render() {
		// settings
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);

		if (is_array($confArr) && count($confArr) > 2) {
			return true;
		}
		return false;
    }
}

?>