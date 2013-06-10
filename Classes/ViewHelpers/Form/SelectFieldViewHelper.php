<?php

/**
 * View helper to generate select field with empty values, preselected, etc...
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Form_SelectFieldViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * Generate Select
     *
     * @param 	array 		Options to render
	 * 		option1 =>
	 * 			label => Red Shoes
	 * 			value => red
	 * 			selected => 1
     * @param 	string 		Class
     * @param 	string 		ID
     * @return 	string		Select field
     */
    public function render($options, $class = '', $id = '') {
		// config
//		$this->registerFieldNameForFormTokenGeneration($this->prefixFieldName($this->getName()));
		$this->registerFieldNameForFormTokenGeneration($this->getName());
		$string = '';

		// select
		$string .= '<select';
		if ($this->getName()) {
//			$string .= ' name="' . $this->prefixFieldName($this->getName()) . '"';
			$string .= ' name="' . $this->getName() . '"';
		}
		if ($class) {
			$string .= ' class="' . $class . '"';
		}
		if ($id) {
			$string .= ' id="' . $id . '"';
		}
		$string .= '>';

		// option
		foreach ($options as $option) {
			$string .= '<option value="' . htmlspecialchars($option['value']) . '"';
			if (
				($option['selected'] && !$this->getValue()) || // preselect from flexform
				($this->getValue() && ($option['value'] == $this->getValue() || $option['label'] == $this->getValue())) // preselect from piVars
			) {
				$string .= ' selected="selected"';
			}
			$string .= '>';
			$string .= htmlspecialchars($option['label']);
			$string .= '</option>';
		}
		$string .= '</select>';

		return $string;
    }
}

?>