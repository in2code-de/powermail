<?php

/**
 * ViewHelper combines Raw and RemoveXss Methods
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_String_RawAndRemoveXssViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * Disable the escaping interceptor because
	 * 		otherwise the child nodes would be escaped before this view helper
	 * 		can decode the text's entities.
	 *
	 * @var boolean
	 */
	protected $escapingInterceptorEnabled = FALSE;

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * ViewHelper combines Raw and RemoveXss Methods
	 *
	 * @return string
	 */
	public function render() {
		$string = $this->renderChildren();

		/** @var Tx_Fluid_View_StandaloneView $parseObject */
		$parseObject = $this->objectManager->create('Tx_Fluid_View_StandaloneView');
		$parseObject->setTemplateSource((string) $string);
		$string = $parseObject->render();

		// remove XSS
		$string = t3lib_div::removeXSS($string);

		return $string;
	}

	/**
	 * Injects the Object Manager
	 *
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}
}