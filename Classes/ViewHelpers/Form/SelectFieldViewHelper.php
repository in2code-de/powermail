<?php
namespace In2code\Powermail\ViewHelpers\Form;

use TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper;

/**
 * View helper to generate select field with empty values, preselected, etc...
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class SelectFieldViewHelper extends SelectViewHelper {

	/**
	 * @var array
	 */
	protected $originalOptions = array();

	/**
	 * Render the tag.
	 *
	 * @return string rendered tag.
	 * @api
	 */
	public function render() {
		$this->originalOptions = $this->arguments['options'];
		$this->setOptions();
		return parent::render();
	}

	/**
	 * Set options with key and value from $field->getModifiedOptions()
	 * 		convert:
	 * 			array(
	 * 				array(
	 * 					'label' => 'Red shoes',
	 * 					'value' => 'red',
	 * 					'selected' => 0
	 * 				)
	 * 			)
	 *
	 * 		to:
	 * 			array(
	 * 				'red' => 'Red shoes'
	 * 			)
	 *
	 *
	 * @return void
	 */
	protected function setOptions() {
		$optionArray = array();
		foreach ($this->arguments['options'] as $option) {
			$optionArray[$option['value']] = $option['label'];
		}
		$this->arguments['options'] = $optionArray;
	}

	/**
	 * Render one option tag
	 *
	 * @param string $value value attribute of the option tag (will be escaped)
	 * @param string $label content of the option tag (will be escaped)
	 * @return string the rendered option tag
	 */
	protected function renderOptionTag($value, $label) {
		return parent::renderOptionTag(
			$value,
			$label,
			$this->isSelectedAlternative($this->getOptionFromOriginalOptionsByValue($value))
		);
	}

	/**
	 * @param string $value
	 * @return array
	 */
	protected function getOptionFromOriginalOptionsByValue($value) {
		foreach ($this->originalOptions as $option) {
			if ($value === $option['value'] || $value === $option['label']) {
				return $option;
			}
		}
		return array();
	}

	/**
	 * Check if option is selected
	 *
	 * @param array $option Current option
	 * @return boolean TRUE if the value marked a s selected; FALSE otherwise
	 */
	protected function isSelectedAlternative($option) {
		if (is_array($this->getValue())) {
			return $this->isSelectedAlternativeForArray($option);
		}
		return $this->isSelectedAlternativeForString($option);
	}

	/**
	 * @param array $option
	 * @return bool
	 */
	protected function isSelectedAlternativeForString($option) {
		if (
			// preselect from flexform
			($option['selected'] && !$this->getValue()) ||
			// preselect from piVars
			($this->getValue() && ($option['value'] === $this->getValue() || $option['label'] === $this->getValue()))
		) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @param array $option
	 * @return bool
	 */
	protected function isSelectedAlternativeForArray($option) {
		foreach ($this->getValue() as $singleValue) {
			if (!empty($singleValue) && ($option['value'] === $singleValue || $option['label'] === $singleValue)) {
				return TRUE;
			}
		}
		return FALSE;
	}
}