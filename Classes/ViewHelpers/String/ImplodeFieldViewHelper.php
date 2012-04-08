<?php

/**
 * View helper to implode objects to a list
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_String_ImplodeFieldViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * View helper to explode a list
     *
     * @param 	objects		Any objects with submethod getUid()
     * @param 	string		Field to use in object
     * @param 	string 		Separator sign (e.g. ",")
     * @return 	string
     */
    public function render($objects, $field = 'uid', $separator = ',') {
		$string = '';
		foreach ($objects as $object) {
			if (method_exists($object, 'get' . ucfirst($field))) {
				$string .= $object->{'get' . ucfirst($field)}();
				$string .= $separator;
			}
		}
		return substr($string, 0, (-1 * strlen($separator)));
    }
}

?>