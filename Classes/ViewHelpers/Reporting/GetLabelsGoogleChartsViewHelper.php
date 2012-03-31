<?php

/**
 * View helper check if given value is array or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Reporting_GetLabelsGoogleChartsViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * View helper check if given value is array or not
     *
     * @param 	array 		Grouped Answers
     * @param 	int 		Field UID
	 * @param 	int 		Crop each label after X signs
	 * @return 	string		"label1|label2|label3"
     */
    public function render($answers, $field, $crop = 15) {
		$string = '';
		if (!isset($answers[$field])) {
			return;
		}

		// create string
		foreach ((array) $answers[$field] as $value => $amount) {
			if (strlen($value) > $crop) {
				$value = substr($value, 0, $crop) . '...';
			}
			$string .= $value;
			$string .= '|';
		}

		return urlencode(substr($string, 0, -1));
    }
}

?>