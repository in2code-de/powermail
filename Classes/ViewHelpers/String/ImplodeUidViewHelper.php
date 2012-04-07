<?php

/**
 * View helper to implode objects to a list
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_String_ImplodeUidViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * View helper to explode a list
     *
     * @param 	objects		Any objects with submethod getUid()
     * @param 	string 		Separator sign (e.g. ",")
     * @return 	string
     */
    public function render($objects, $separator = ',') {
		$string = '';
		foreach ($objects as $object) {
			if (method_exists($object, 'getUid')) {
				$string .= $object->getUid();
				$string .= $separator;
			}
		}
		return substr($string, 0, (-1 * strlen($separator)));
    }
}

?>