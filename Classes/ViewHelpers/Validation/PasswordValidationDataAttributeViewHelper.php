<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Returns Data-Attributes for JS and Native Validation
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class PasswordValidationDataAttributeViewHelper extends ValidationDataAttributeViewHelper {

	/**
	 * Returns Data Attribute Array for JS validation with parsley.js
	 *
	 * @param Field $field
	 * @param array $additionalAttributes To add further attributes
	 * @param mixed $iteration Iterationarray for Multi Fields (Radio, Check, ...)
	 * @return array for data attributes
	 */
	public function render(Field $field, $additionalAttributes = array(), $iteration = NULL) {
		$additionalAttributes = parent::render($field, $additionalAttributes, $iteration);

		if ($this->isClientValidationEnabled()) {
			$additionalAttributes['data-parsley-equalto'] = '#powermail_field_' . $field->getMarker();
			$additionalAttributes['data-parsley-equalto-message'] = LocalizationUtility::translate(
				'validationerror_password',
				$this->extensionName
			);
		}

		return $additionalAttributes;
	}
}