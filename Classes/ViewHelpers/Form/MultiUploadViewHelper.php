<?php
namespace In2code\Powermail\ViewHelpers\Form;

/**
 * View helper to get a country array
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class MultiUploadViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\UploadViewHelper {

	/**
	 * Initialize the arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
	}

	/**
	 * Renders the upload field.
	 *
	 * @return string
	 */
	public function render() {
		$name = $this->getName();
		$allowedFields = array('name', 'type', 'tmp_name', 'error', 'size');
		foreach ($allowedFields as $fieldName) {
			$this->registerFieldNameForFormTokenGeneration($name . '[' . $fieldName . '][]');
		}
		$this->tag->addAttribute('type', 'file');
		$name .= '[]';
		$this->tag->addAttribute('name', $name);
		$this->setErrorClassAttribute();
		return $this->tag->render();
	}
}