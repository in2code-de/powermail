<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Form;

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
	 * @param array $additionalAttributes To add further attributes
	 * @return array for data attributes
	 */
	public function render(Form $form, $additionalAttributes = array()) {
		if ($this->isClientValidationEnabled()) {
			$additionalAttributes['data-parsley-validate'] = 'data-parsley-validate';
		}

		if ($this->isNativeValidationEnabled()) {
			$additionalAttributes['data-validate'] = 'html5';
		}

		if ($this->settings['misc']['ajaxSubmit'] === '1') {
			$additionalAttributes['data-powermail-ajax'] = 'true';
			$additionalAttributes['data-powermail-form'] = $form->getUid();
		}

		return $additionalAttributes;
	}
}