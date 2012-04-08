<?php

/**
 * View helper encoding of URL for RSS Feeds
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Powermail_ViewHelpers_String_EncodeViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * Returns encoded string
     *
     * @return 	string		Encoded string
     */
    public function render() {
		$string = htmlspecialchars($this->renderChildren());
		return $string;
    }
}

?>