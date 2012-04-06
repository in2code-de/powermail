<?php

class Tx_Powermail_ViewHelpers_Condition_OrViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * OR viewhelper for if widget in fluid
     *
     * @param 	array		Array with strings
     * @param 	string		String to compare (if empty, just check array if there are values)
     * @return 	boolean		true/false
     */
    public function render($array, $string = null) {
		foreach ((array) $array as $value) {
			if (!$string && $value) {
				return true;
			}
			if ($string && $value == $string) {
				return true;
			}
		}
		return false;
    }
}

?>