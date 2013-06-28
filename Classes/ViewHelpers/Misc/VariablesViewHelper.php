<?php

/**
 * Parses Variables for powermail
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Misc_VariablesViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * Div Methods
	 *
	 * @var		Tx_Powermail_Utility_Div
	 */
	protected $div;

	/**
	 * Configuration
	 */
	protected $settings;

	/**
	 * Parses variables again
	 *
	 * @param	array		Variables and Markers array
	 * @param	array		Variables and Labels array
	 * @param	string		"web" or "mail"
	 * @return 	string		Changed string
	 */
	public function render($variablesMarkers = array(), $variablesLabels = array(), $type = 'web') {
		$parseObject = $this->objectManager->create('Tx_Fluid_View_StandaloneView');
		$parseObject->setTemplateSource($this->renderChildren());
		$parseObject->assignMultiple($this->div->htmlspecialcharsOnArray($variablesMarkers));

		$powermailAll = $this->div->powermailAll($variablesLabels, $this->configurationManager, $this->objectManager, $type, $this->settings);
		$parseObject->assign('powermail_all', $powermailAll);

		return html_entity_decode($parseObject->render());
	}

	/**
	 * Injects the Configuration Manager
	 *
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	*/
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Injects the Object Manager
	 *
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
		$typoScriptSetup = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$this->settings = t3lib_div::removeDotsFromTS($typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.']);
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->div = t3lib_div::makeInstance('Tx_Powermail_Utility_Div');
	}

}

?>