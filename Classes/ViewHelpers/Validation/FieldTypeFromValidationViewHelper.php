<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;

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
		3 => 'tel',
		4 => 'number',
		8 => 'range'
	);

	/**
	 * Parses variables again
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @return string
	 */
	public function render(Field $field) {
		if (!$this->isNativeValidationEnabled()) {
			return 'text';
		}
		if (array_key_exists($field->getValidation(), $this->html5InputTypes)) {
			return $this->html5InputTypes[$field->getValidation()];
		}
		return 'text';
	}

}