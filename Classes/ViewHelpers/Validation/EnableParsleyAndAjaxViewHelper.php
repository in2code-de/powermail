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
	 * @param Form $form
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
			if ($this->getRedirectUri()) {
				$additionalAttributes['data-powermail-ajax-uri'] = $this->getRedirectUri();
			}
		}

		return $additionalAttributes;
	}

	/**
	 * Get redirect URI from FlexForm or TypoScript
	 *
	 * @return NULL|string
	 */
	protected function getRedirectUri() {
		$uriBuilder = $this->controllerContext->getUriBuilder();
		$target = NULL;

		// target from flexform
		$flexFormArray = $this->getFlexFormArray();
		if (!empty($flexFormArray['thx']['lDEF']['settings.flexform.thx.redirect']['vDEF'])) {
			$target = $flexFormArray['thx']['lDEF']['settings.flexform.thx.redirect']['vDEF'];
		}

		// target from TypoScript overwrite
		if (!empty($this->settings['thx']['overwrite']['redirect'])) {
			$target = $this->contentObject->cObjGetSingle(
				$this->settings['thx']['overwrite']['redirect']['_typoScriptNodeValue'],
				$this->settings['thx']['overwrite']['redirect']);
		}

		if ($target) {
			$uriBuilder->setTargetPageUid($target);
			$target = $uriBuilder->build();
		}
		return $target;
	}
}
