<?php

/**
 * Used in the Backendmodule to get a defined piVar
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Getter_GetPiVarAnswerFieldViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Used in the Backendmodule to get a defined piVar
	 *
	 * @param 	int			Field UID
	 * @param 	array		Plugin Vars
	 * @return	string		parsed Variable
	 */
	public function render($fieldUid, $piVars) {
		if (isset($piVars['filter']['answer'][$fieldUid])) {
			return htmlspecialchars($piVars['filter']['answer'][$fieldUid]);
		}
	}
}

?>