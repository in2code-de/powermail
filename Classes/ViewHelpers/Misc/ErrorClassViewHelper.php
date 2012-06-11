<?php

/**
 * Returns Error Class if Error in form
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Powermail_ViewHelpers_Misc_ErrorClassViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * Prefill string for fields
     *
     * @param 	object		Current field
     * @param 	string 		Any string for errorclass
     * @return 	string		Changed string
     */
    public function render($field, $class) {

		// get all errors
		// @TODO remove the deprecated method call
		$errors = $this->controllerContext->getRequest()->getErrors();
		foreach ($errors as $key => $error) {
			if ($key != 'field') {
				continue;
			}

			// we want the field errors
			$fieldErrors = $error->getErrors();
			foreach ($fieldErrors as $fieldError) {
				if ($field->getUid() == $fieldError->getCode()) {
					return $class;
				}
			}
		}

    }
}

?>