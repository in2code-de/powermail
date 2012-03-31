<?php

/**
 * View helper check if given value is array or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Reporting_GetValuesGoogleChartsViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * View helper check if given value is array or not
     *
     * @param 	array 		Grouped Answers
     * @param 	int 		Field UID
     * @param 	string 		Separator
	 * @return 	string		"label1|label2|label3"
     */
    public function render($answers, $field, $separator = ',') {
		$string = '';
		if (!isset($answers[$field])) {
			return;
		}

		// create string
		foreach ((array) $answers[$field] as $value => $amount) {
			$string .= $amount;
			$string .= $separator;
		}

		return urlencode(substr($string, 0, -1));
    }
}

?>