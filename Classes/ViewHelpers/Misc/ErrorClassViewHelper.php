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
	 * @param object $field Current field
	 * @param string $class Any string for errorclass
	 * @return string Changed string
	 */
	public function render($field, $class) {
		if (method_exists($this->controllerContext->getRequest(), 'getOriginalRequestMappingResults')) {
			// newer TYPO3 versions
			/* @var TYPO3\CMS\Extbase\Error\Result $validationResults */
			$validationResults = $this->controllerContext->getRequest()->getOriginalRequestMappingResults();
			$errors = $validationResults->getFlattenedErrors();
			foreach ($errors as $error) {
				foreach ((array) $error as $singleError) {
					if ($field->getUid() === $singleError->getCode()) {
						return $class;
					}
				}
			}
		} else {

			// older TYPO3 versions
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
		return '';

	}
}