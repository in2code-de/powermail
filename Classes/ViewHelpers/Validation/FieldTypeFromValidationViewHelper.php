<?php
namespace In2code\Powermail\ViewHelpers\Validation;

/**
 * Get Field Type for input fields
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class FieldTypeFromValidationViewHelper extends AbstractValidationViewHelper {

	/**
	 * InputTypes
	 *
	 * @var array
	 */
	protected $html5InputTypes = array(
		1 => 'email',
		2 => 'url',
		4 => 'number',
		8 => 'range'
	);

	/**
	 * Parses variables again
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @return string
	 */
	public function render(\In2code\Powermail\Domain\Model\Field $field) {
		if (!$this->isNativeValidationEnabled()) {
			return 'text';
		}
		if (array_key_exists($field->getValidation(), $this->html5InputTypes)) {
			return $this->html5InputTypes[$field->getValidation()];
		}
		return 'text';
	}

}