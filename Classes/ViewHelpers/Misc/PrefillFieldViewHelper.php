<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Prefill a field with variables
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class PrefillFieldViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var array
	 */
	protected $piVars;

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $cObj;

	/**
	 * Prefill string for fields
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \In2code\Powermail\Domain\Model\Mail $mail To prefill in Edit Action
	 * @param \int $cycle Cycle Number (1,2,3...) - if filled checkbox or radiobutton
	 * @return mixed Prefill field with this string
	 */
	public function render(
		\In2code\Powermail\Domain\Model\Field $field, \In2code\Powermail\Domain\Model\Mail $mail = NULL, $cycle = 0) {

		if ($cycle === 0) {
			$value = $this->getDefaultValue($field, $mail);
		} else {
			$value = $this->getMultiValue($field, $mail, $cycle);
		}

		return $value;
	}

	/**
	 * Get value for default fieldtypes (input, textarea, hidden, select)
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \In2code\Powermail\Domain\Model\Mail $mail To prefill in Edit Action
	 * @return string|array
	 */
	protected function getDefaultValue(
		\In2code\Powermail\Domain\Model\Field $field, \In2code\Powermail\Domain\Model\Mail $mail = NULL) {

		$value = '';
		$marker = $field->getMarker();
		$uid = $field->getUid();

		// edit view
		if ($mail !== NULL && $mail->getAnswers()) {
			foreach ($mail->getAnswers() as $answer) {
				if ($answer->getField() === $field) {
					$value = $answer->getValue();
				}
			}
		}

		// if GET/POST with marker (&tx_powermail_pi1[field][marker]=value)
		if (isset($this->piVars['field'][$marker])) {
			$value = $this->piVars['field'][$marker];
		}

		// if GET/POST with marker (&tx_powermail_pi1[marker]=value)
		elseif (isset($this->piVars[$marker])) {
			$value = $this->piVars[$marker];
		}

		// if GET/POST with new uid (&tx_powermail_pi1[field][123]=value)
		elseif (isset($this->piVars['field'][$uid])) {
			$value = $this->piVars['field'][$uid];
		}

		// if GET/POST with old uid (&tx_powermail_pi1[uid123]=value)
		elseif (isset($this->piVars['uid' . $uid])) {
			$value = $this->piVars['uid' . $uid];
		}

		// if field should be filled with FE_User values
		elseif ($field->getFeuserValue()) {
			// if fe_user is logged in
			if (intval($GLOBALS['TSFE']->fe_user->user['uid']) !== 0) {
				$value = $GLOBALS['TSFE']->fe_user->user[$field->getFeuserValue()];
			}
		}

		// if prefill value (from flexform)
		elseif ($field->getPrefillValue()) {
			$value = $field->getPrefillValue();
		}

		// if prefill value (from typoscript)
		elseif (isset($this->settings['prefill.'][$marker]) || isset($this->settings['prefill.'][$marker . '.'])) {
			if (isset($this->settings['prefill.'][$marker . '.']) && is_array($this->settings['prefill.'][$marker . '.'])) {
				$data = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getGettableProperties($field);
				$this->cObj->start($data);

				if (isset($this->settings['prefill.'][$marker . '.']['0'])) {

					/**
					 * plugin.tx_powermail.settings.setup.prefill.marker.0 = TEXT
					 * plugin.tx_powermail.settings.setup.prefill.marker.0.value = red
					 */
					$value = array();
					foreach (array_keys($this->settings['prefill.'][$marker . '.']) as $key) {
						if (stristr($key, '.')) {
							continue;
						}
						$value[] = $this->cObj->cObjGetSingle(
							$this->settings['prefill.'][$marker . '.'][$key],
							$this->settings['prefill.'][$marker . '.'][$key . '.']
						);
					}
				} else {

					/**
					 * plugin.tx_powermail.settings.setup.prefill.marker = TEXT
					 * plugin.tx_powermail.settings.setup.prefill.marker.value = red
					 */
					$value = $this->cObj->cObjGetSingle($this->settings['prefill.'][$marker], $this->settings['prefill.'][$marker . '.']);
				}
			} else {

				/**
				 * plugin.tx_powermail.settings.setup.prefill.marker = red
				 */
				$value = $this->settings['prefill.'][$marker];
			}

		}

		return $value;
	}

	/**
	 * Get value for multi fieldtypes (checkbox, radio)
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \In2code\Powermail\Domain\Model\Mail $mail To prefill in Edit Action
	 * @param \int $cycle Cycle Number (1,2,3...) - if filled checkbox or radiobutton
	 * @return string
	 */
	protected function getMultiValue(
		\In2code\Powermail\Domain\Model\Field $field, \In2code\Powermail\Domain\Model\Mail $mail = NULL, $cycle = 0) {
		$marker = $field->getMarker();
		$uid = $field->getUid();
		$selected = 0;
		$index = $cycle - 1;
		$options = $field->getModifiedSettings();

		// edit view
		if ($mail !== NULL && $mail->getAnswers()) {
			foreach ($mail->getAnswers() as $answer) {
				if ($answer->getField() === $field) {
					$values = $answer->getValue();
					foreach ((array) $values as $value) {
						if ($value === $options[$index]['value'] || $value === $options[$index]['label']) {
							$selected = 1;
						}
					}
				}
			}
		}

		// if GET/POST with marker (&tx_powermail_pi1[field][marker][index]=value)
		if (isset($this->piVars['field'][$marker]) && is_array($this->piVars['field'][$marker])) {
			foreach (array_keys($this->piVars['field'][$marker]) as $key) {
				if (
					$this->piVars['field'][$marker][$key] === $options[$index]['value']
					|| $this->piVars['field'][$marker][$key] === $options[$index]['label']
				) {
					$selected = 1;
				}
			}
		}

		// if GET/POST with marker (&tx_powermail_pi1[field][marker]=value)
		elseif (isset($this->piVars['field'][$marker])) {
			if ($this->piVars['field'][$marker] == $options[$index]['value'] ||
				$this->piVars['field'][$marker] == $options[$index]['label']) {
				$selected = 1;
			}
		}

		// if GET/POST with marker (&tx_powermail_pi1[marker][index]=value)
		elseif (isset($this->piVars[$marker]) && is_array($this->piVars[$marker])) {
			foreach (array_keys($this->piVars[$marker]) as $key) {
				if (
					$this->piVars[$marker][$key] === $options[$index]['value']
					|| $this->piVars[$marker][$key] === $options[$index]['label']
				) {
					$selected = 1;
				}
			}
		}

		// if GET/POST with marker (&tx_powermail_pi1[marker]=value)
		elseif (isset($this->piVars[$marker])) {
			if ($this->piVars[$marker] == $options[$index]['value'] || $this->piVars[$marker] == $options[$index]['label']) {
				$selected = 1;
			}
		}

		// if GET/POST with new uid (&tx_powermail_pi1[field][123]=value)
		elseif (isset($this->piVars['field'][$uid])) {
			if (is_array($this->piVars['field'][$uid])) {
				foreach ($this->piVars['field'][$uid] as $key => $value) {
					$value = NULL;
					if ($this->piVars['field'][$uid][$key] == $options[$index]['value'] ||
						$this->piVars['field'][$uid][$key] == $options[$index]['label']) {
						$selected = 1;
					}
				}
			} else {
				if ($this->piVars['field'][$uid] == $options[$index]['value'] || $this->piVars['field'][$uid] == $options[$index]['label']) {
					$selected = 1;
				}
			}
		}

		// if GET/POST with old uid (&tx_powermail_pi1[uid123]=value)
		elseif (isset($this->piVars['uid' . $uid])) {
			if ($this->piVars['uid' . $uid] == $options[$index]['value'] || $this->piVars['uid' . $uid] == $options[$index]['label']) {
				$selected = 1;
			}
		}

		// if field should be filled with FE_User values
		elseif ($field->getFeuserValue() && intval($GLOBALS['TSFE']->fe_user->user['uid']) !== 0) {
			// if fe_user is logged in
			if ($GLOBALS['TSFE']->fe_user->user[$field->getFeuserValue()] == $options[$index]['value'] ||
				$GLOBALS['TSFE']->fe_user->user[$field->getFeuserValue()] == $options[$index]['label']) {
				$selected = 1;
			}
		}

		// if prefill value (from flexform)
		elseif ($options[$index]['selected']) {
			$selected = 1;
		}

		// if prefill value (from typoscript)
		elseif (isset($this->settings['prefill.'][$marker]) || isset($this->settings['prefill.'][$marker . '.'])) {
			if (isset($this->settings['prefill.'][$marker . '.']) && is_array($this->settings['prefill.'][$marker . '.'])) {
				$data =  \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getGettableProperties($field);
				$this->cObj->start($data);

				if (isset($this->settings['prefill.'][$marker . '.']['0'])) {

					/**
					 * plugin.tx_powermail.settings.setup.prefill.marker.0 = TEXT
					 * plugin.tx_powermail.settings.setup.prefill.marker.0.value = red
					 */
					foreach (array_keys($this->settings['prefill.'][$marker . '.']) as $key) {
						if (stristr($key, '.')) {
							continue;
						}
						$prefill = $this->cObj->cObjGetSingle(
							$this->settings['prefill.'][$marker . '.'][$key],
							$this->settings['prefill.'][$marker . '.'][$key . '.']
						);
						if ($prefill == $options[$index]['value'] || $prefill == $options[$index]['label']) {
							$selected = 1;
						}
					}
				} else {

					/**
					 * plugin.tx_powermail.settings.setup.prefill.marker = TEXT
					 * plugin.tx_powermail.settings.setup.prefill.marker.value = red
					 */
					$prefill = $this->cObj->cObjGetSingle($this->settings['prefill.'][$marker], $this->settings['prefill.'][$marker . '.']);
					if ($prefill == $options[$index]['value'] || $prefill == $options[$index]['label']) {
						$selected = 1;
					}
				}
			} else {

				/**
				 * plugin.tx_powermail.settings.setup.prefill.marker = red
				 */
				if ($this->settings['prefill.'][$marker] == $options[$index]['value'] ||
					$this->settings['prefill.'][$marker] == $options[$index]['label']) {
					$selected = 1;
				}
			}

		}

		return $selected;
	}

	/**
	 * Inject Configuration Manager
	 *
	 * @param ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->cObj = $this->configurationManager->getContentObject();
		$typoScriptSetup = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
		);
		$this->settings = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
	}

	/**
	 * Object initialization
	 *
	 * @return void
	 */
	public function initializeObject() {
		$this->piVars = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_powermail_pi1');
	}
}