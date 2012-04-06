<?php

class Tx_Powermail_ViewHelpers_Condition_OrViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * OR viewhelper for if widget in fluid
     *
     * @param 	array		Array with strings
     * @return 	boolean		true/false
     */
    public function render($array) {
		foreach ((array) $array as $value) {
			if (!$value) {
				return true;
			}
		}
		return false;
    }
}

?>