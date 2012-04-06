<?php

/**
 * Remove Quotes from Inner HTML
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_String_RemoveQuoteViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * Remove Quotes from Inner HTML
     *
     * @return 	boolean
     */
    public function render() {
		$string = str_replace('"', '\'', $this->renderChildren());

		return $string;
    }
}

?>