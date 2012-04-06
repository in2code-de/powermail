<?php

/**
 * utf8_decode for Inner HTML
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_String_Utf8DecodeViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * utf8_decode for Inner HTML
     *
     * @return 	string
     */
    public function render() {
		return utf8_decode($this->renderChildren());
    }
}

?>