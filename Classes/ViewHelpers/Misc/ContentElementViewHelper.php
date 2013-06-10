<?php

/**
 * Shows Content Element
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Misc_ContentElementViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var Content Object
	 */
	protected $cObj;

    /**
     * Parse a content element
     *
	 * @param	int			UID of any content element
     * @return 	string		Parsed Content Element
     */
    public function render($uid) {
		$conf = array( // config
			'tables' => 'tt_content',
			'source' => $uid,
			'dontCheckPid' => 1
		);
		return $this->cObj->RECORDS($conf);
    }

	/**
	 * Injects the Configuration Manager
	 *
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	*/
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->cObj = $this->configurationManager->getContentObject();
	}

}

?>