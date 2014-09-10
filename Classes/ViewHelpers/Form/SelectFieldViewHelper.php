<?php
namespace In2code\Powermail\ViewHelpers\Form;

/**
 * View helper to generate select field with empty values, preselected, etc...
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class SelectFieldViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper {

	/**
	 * Render the tag.
	 *
	 * @return string rendered tag.
	 * @api
	 */
	public function render() {
		$content = parent::render();

		return $content;
	}

	/**
	 * Render the option tags.
	 *
	 * @param array $options the options for the form.
	 * @return string rendered tags.
	 */
	protected function renderOptionTags($options) {
		$output = '';
		foreach ($options as $option) {
			$output .= $this->renderOptionTag(
				$option['value'],
				$option['label'],
				$this->isSelectedAlternative($option)
			);
			$output .= chr(10);
		}
		return $output;
	}

	/**
	 * Check if option is selected
	 *
	 * @param array $option Current option
	 * @return boolean TRUE if the value marked a s selected; FALSE otherwise
	 */
	protected function isSelectedAlternative($option) {
		if (!is_array($this->getValue())) {
			if (
				// preselect from flexform
				($option['selected'] && !$this->getValue()) ||
				// preselect from piVars
				($this->getValue() && ($option['value'] === $this->getValue() || $option['label'] === $this->getValue()))
			) {
				return TRUE;
			}
		} else {
			// Multi Select
			foreach ($this->getValue() as $singleValue) {
				if (!empty($singleValue) && ($option['value'] === $singleValue || $option['label'] === $singleValue)) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}
}