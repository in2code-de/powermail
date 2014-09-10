<?php
namespace In2code\Powermail\ViewHelpers\Validation;

/**
 * Returns Error Class if Error in form
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class ErrorClassViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Prefill string for fields
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param string $class Any string for errorclass
	 * @return string
	 */
	public function render(\In2code\Powermail\Domain\Model\Field $field, $class = 'error') {
		$validationResults = $this->controllerContext->getRequest()->getOriginalRequestMappingResults();
		$errors = $validationResults->getFlattenedErrors();
		foreach ($errors as $error) {
			foreach ((array) $error as $singleError) {
				if ($field->getMarker() === $singleError->getCode()) {
					return $class;
				}
			}
		}
		return '';
	}
}