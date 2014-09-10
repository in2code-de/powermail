<?php
namespace In2code\Powermail\ViewHelpers\Validation;

/**
 * Adds additional attributes for parsley or AJAX submit
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class EnableParsleyAndAjaxViewHelper extends AbstractValidationViewHelper {

	/**
	 * Returns Data Attribute Array to enable parsley
	 *
	 * @param \In2code\Powermail\Domain\Model\Form $form
	 * @param \array $additionalAttributes To add further attributes
	 * @return \array for data attributes
	 */
	public function render(\In2code\Powermail\Domain\Model\Form $form, $additionalAttributes = array()) {
		$dataArray = $additionalAttributes;

		// add parsley attribute
		if ($this->isClientValidationEnabled()) {
			$dataArray['data-parsley-validate'] = 'data-parsley-validate';
		}

		// add attribute for html5 validation
		if ($this->isNativeValidationEnabled()) {
			$dataArray['data-validate'] = 'html5';
		}

		// add ajax attribute
		if ($this->settings['misc']['ajaxSubmit'] === '1') {
			$dataArray['data-powermail-ajax'] = 'true';
			$dataArray['data-powermail-form'] = $form->getUid();
		}

		return $dataArray;
	}
}