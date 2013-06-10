<?php

/**
 * Prefill a field with variables
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Powermail_ViewHelpers_Misc_PrefillFieldViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var settings
	 */
	protected $settings;

	/**
	 * @var piVars
	 */
	protected $piVars;

	/**
	 * @var Content Object
	 */
	protected $cObj;

    /**
     * Prefill string for fields
     *
     * @param 	object 		$field Field Object
     * @param 	int 		$cycle Cycle Number (1,2,3...) - if filled it's a checkbox or radiobutton
     * @param 	string 		$overwrite Value (Overwrites everything)
     * @return 	string		Prefill field with this string
     */
    public function render($field, $cycle = 0) {
		// config
		$value = '';
		$marker = $field->getMarker();
		$uid = $field->getUid();

		// Default fieldtypes (input, textarea, hidden, select)
		if ($cycle == 0) {

			// if GET/POST with marker (&tx_powermail_pi1[marker]=value)
			if (isset($this->piVars[$marker])) {
				$value = $this->piVars[$marker];
			}

			// if GET/POST with new uid (&tx_powermail_pi1[field][123]=value)
			elseif (isset($this->piVars['field'][$uid])) {
				$value = $this->piVars['field'][$uid];
			}

			// if GET/POST with old uid (&tx_powermail_pi1[uid123]=value (downgrade to powermail < 2)
			elseif (isset($this->piVars['uid' . $uid])) {
				$value = $this->piVars['uid' . $uid];
			}

			// if field should be filled with FE_User values
			elseif ($field->getFeuserValue()) {
				if (intval($GLOBALS['TSFE']->fe_user->user['uid']) !== 0) { // if fe_user is logged in
					$value = $GLOBALS['TSFE']->fe_user->user[$field->getFeuserValue()];
				}
			}

			// if prefill value (from flexform)
			elseif ($field->getPrefillValue()) {
				$value = $field->getPrefillValue();
			}

			// if prefill value (from typoscript)
			elseif ($this->settings['prefill.'][$marker]) {
				if (isset($this->settings['prefill.'][$marker . '.']) && is_array($this->settings['prefill.'][$marker . '.'])) { // Parse cObject
					$data =  Tx_Extbase_Reflection_ObjectAccess::getGettableProperties($field); // make array from object
					$this->cObj->start($data); // push to ts
					$value = $this->cObj->cObjGetSingle($this->settings['prefill.'][$marker], $this->settings['prefill.'][$marker . '.']); // parse
				} else { // Use String only
					$value = $this->settings['prefill.'][$marker];
				}

			}

			return $value;



		// Check, Radio
		} else {
			$selected = 0;
			$index = $cycle - 1;
			$options = $field->getSettings();

			// if GET/POST with marker (&tx_powermail_pi1[marker]=value)
			if (isset($this->piVars[$marker])) {
				if ($this->piVars[$marker] == $options[$index]['value'] || $this->piVars[$marker] == $options[$index]['label']) {
					$selected = 1;
				}
			}

			// if GET/POST with new uid (&tx_powermail_pi1[field][123]=value)
			elseif (isset($this->piVars['field'][$uid])) {
				if (is_array($this->piVars['field'][$uid])) {
					foreach ($this->piVars['field'][$uid] as $key => $value) {
						if ($this->piVars['field'][$uid][$key] == $options[$index]['value'] || $this->piVars['field'][$uid][$key] == $options[$index]['label']) {
							$selected = 1;
						}
					}
				} else {
					if ($this->piVars['field'][$uid] == $options[$index]['value'] || $this->piVars['field'][$uid] == $options[$index]['label']) {
						$selected = 1;
					}
				}
			}

			// if GET/POST with old uid (&tx_powermail_pi1[uid123]=value (downgrade to powermail < 2)
			elseif (isset($this->piVars['uid' . $uid])) {
				if ($this->piVars['uid' . $uid] == $options[$index]['value'] || $this->piVars['uid' . $uid] == $options[$index]['label']) {
					$selected = 1;
				}
			}

			// if field should be filled with FE_User values
			elseif ($field->getFeuserValue() && intval($GLOBALS['TSFE']->fe_user->user['uid']) !== 0) {
				if ($GLOBALS['TSFE']->fe_user->user[$field->getFeuserValue()] == $options[$index]['value'] || $GLOBALS['TSFE']->fe_user->user[$field->getFeuserValue()] == $options[$index]['label']) { // if fe_user is logged in
					$selected = 1;
				}
			}

			// if prefill value (from flexform)
			elseif ($options[$index]['selected']) {
				$selected = 1;
			}

			// if prefill value (from typoscript)
			elseif ($this->settings['prefill.'][$marker]) {
				if (isset($this->settings['prefill.'][$marker . '.']) && is_array($this->settings['prefill.'][$marker . '.'])) { // Parse cObject
					$data =  Tx_Extbase_Reflection_ObjectAccess::getGettableProperties($field); // make array from object
					$this->cObj->start($data); // push to ts
					if (
						$this->cObj->cObjGetSingle($this->settings['prefill.'][$marker], $this->settings['prefill.'][$marker . '.']) == $options[$index]['value'] ||
						$this->cObj->cObjGetSingle($this->settings['prefill.'][$marker], $this->settings['prefill.'][$marker . '.']) == $options[$index]['label']
					) {
						$selected = 1;
					}
				} else { // Use String only
					if ($this->settings['prefill.'][$marker] == $options[$index]['value'] || $this->settings['prefill.'][$marker] == $options[$index]['label']) {
						$selected = 1;
					}
				}

			}

			return $selected;
		}

    }

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->cObj = $this->configurationManager->getContentObject();
		$this->typoScriptSetup = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$this->settings = $this->typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
		$this->piVars = t3lib_div::_GP('tx_powermail_pi1');
	}
}

?>