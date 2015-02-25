<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use In2code\Powermail\Domain\Model\Field;

/**
 * Returns Data-Attributes for JS and Native Validation
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class CaptchaDataAttributeViewHelper extends ValidationDataAttributeViewHelper {

	/**
	 * Returns Data Attribute Array for JS validation with parsley.js
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \array $additionalAttributes To add further attributes
	 * @param \mixed $iteration Iterationarray for Multi Fields (Radio, Check, ...)
	 * @return \array for data attributes
	 */
	public function render(Field $field, $additionalAttributes = array(), $iteration = NULL) {
		if ($field->getType() !== 'captcha') {
			return $additionalAttributes;
		}
		$dataArray = parent::render($field, $additionalAttributes, $iteration);

		if ($this->isNativeValidationEnabled()) {
			$dataArray['required'] = 'required';
		} elseif ($this->isClientValidationEnabled()) {
			$dataArray['data-parsley-required'] = 'true';
		}
		if ($this->isClientValidationEnabled()) {
			$dataArray['data-parsley-errors-container'] = '.powermail_field_error_container_' . $field->getMarker();
			$dataArray['data-parsley-class-handler'] = '.powermail_fieldwrap_' . $field->getUid() . ' > div';
			$dataArray['data-parsley-required-message'] = LocalizationUtility::translate(
				'validationerror_mandatory',
				$this->extensionName
			);
		}

		return $dataArray;
	}
}