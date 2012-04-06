<?php

/**
 * Returns Morestep Class if morestep is given
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Powermail_ViewHelpers_Misc_MorestepClassViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * Returns CSS class for morestep
     *
     * @param 	boolean		Current field
     * @param 	string 		Any string for class
     * @return 	string		Class
     */
    public function render($activate, $class) {
		if ($activate) {
			return $class;
		}
		return '';
    }
}

?>