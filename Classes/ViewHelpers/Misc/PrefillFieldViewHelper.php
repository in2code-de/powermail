<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\Configuration;

/**
 * Prefill a field with variables
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class PrefillFieldViewHelper extends AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
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
	protected $contentObjectRenderer;

	/**
	 * Prefill string for fields
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \In2code\Powermail\Domain\Model\Mail $mail To prefill in Edit Action
	 * @param \int $cycle Cycle Number (1,2,3...) - if filled checkbox or radiobutton
	 * @return string|array|bool Prefill field with this string
	 */
	public function render(Field $field, Mail $mail = NULL, $cycle = 0) {
		// don't prefill if cached form to prevent wrong cached values
		if ($this->isCachedForm()) {
			return '';
		}

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
	protected function getDefaultValue(Field $field, Mail $mail = NULL) {
		$value = '';
		$marker = $field->getMarker();
		$uid = $field->getUid();

		// edit view
		if ($mail !== NULL && $mail->getAnswers()) {
			foreach ($mail->getAnswers() as $answer) {
				if ($answer->getField() === $field) {
					$value = $answer->getValue();
					if (is_array($value)) {
						$value = $value[0];
					}
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
				$this->contentObjectRenderer->start(ObjectAccess::getGettableProperties($field));

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
						$value[] = $this->contentObjectRenderer->cObjGetSingle(
							$this->settings['prefill.'][$marker . '.'][$key],
							$this->settings['prefill.'][$marker . '.'][$key . '.']
						);
					}
				} else {

					/**
					 * plugin.tx_powermail.settings.setup.prefill.marker = TEXT
					 * plugin.tx_powermail.settings.setup.prefill.marker.value = red
					 */
					$value = $this->contentObjectRenderer->cObjGetSingle(
						$this->settings['prefill.'][$marker],
						$this->settings['prefill.'][$marker . '.']
					);
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
	 * @return bool
	 */
	protected function getMultiValue(Field $field, Mail $mail = NULL, $cycle = 0) {
		$marker = $field->getMarker();
		$uid = $field->getUid();
		$selected = FALSE;
		$index = $cycle - 1;
		$options = $field->getModifiedSettings();

		// edit view
		if ($mail !== NULL && $mail->getAnswers()) {
			foreach ($mail->getAnswers() as $answer) {
				if ($answer->getField() === $field) {
					$values = $answer->getValue();
					foreach ((array) $values as $value) {
						if ($value === $options[$index]['value'] || $value === $options[$index]['label']) {
							$selected = TRUE;
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
					$selected = TRUE;
				}
			}
		}

		// if GET/POST with marker (&tx_powermail_pi1[field][marker]=value)
		elseif (isset($this->piVars['field'][$marker])) {
			if ($this->piVars['field'][$marker] === $options[$index]['value'] ||
				$this->piVars['field'][$marker] === $options[$index]['label']) {
				$selected = TRUE;
			}
		}

		// if GET/POST with marker (&tx_powermail_pi1[marker][index]=value)
		elseif (isset($this->piVars[$marker]) && is_array($this->piVars[$marker])) {
			foreach (array_keys($this->piVars[$marker]) as $key) {
				if (
					$this->piVars[$marker][$key] === $options[$index]['value']
					|| $this->piVars[$marker][$key] === $options[$index]['label']
				) {
					$selected = TRUE;
				}
			}
		}

		// if GET/POST with marker (&tx_powermail_pi1[marker]=value)
		elseif (isset($this->piVars[$marker])) {
			if ($this->piVars[$marker] === $options[$index]['value'] || $this->piVars[$marker] === $options[$index]['label']) {
				$selected = TRUE;
			}
		}

		// if GET/POST with new uid (&tx_powermail_pi1[field][123]=value)
		elseif (isset($this->piVars['field'][$uid])) {
			if (is_array($this->piVars['field'][$uid])) {
				foreach ($this->piVars['field'][$uid] as $key => $value) {
					$value = NULL;
					if ($this->piVars['field'][$uid][$key] === $options[$index]['value'] ||
						$this->piVars['field'][$uid][$key] === $options[$index]['label']) {
						$selected = TRUE;
					}
				}
			} else {
				if (
					$this->piVars['field'][$uid] === $options[$index]['value'] ||
					$this->piVars['field'][$uid] === $options[$index]['label']
				) {
					$selected = TRUE;
				}
			}
		}

		// if GET/POST with old uid (&tx_powermail_pi1[uid123]=value)
		elseif (isset($this->piVars['uid' . $uid])) {
			if ($this->piVars['uid' . $uid] === $options[$index]['value'] || $this->piVars['uid' . $uid] === $options[$index]['label']) {
				$selected = TRUE;
			}
		}

		// if field should be filled with FE_User values
		elseif ($field->getFeuserValue() && intval($GLOBALS['TSFE']->fe_user->user['uid']) !== 0) {
			// if fe_user is logged in
			if ($GLOBALS['TSFE']->fe_user->user[$field->getFeuserValue()] === $options[$index]['value'] ||
				$GLOBALS['TSFE']->fe_user->user[$field->getFeuserValue()] === $options[$index]['label']) {
				$selected = TRUE;
			}
		}

		// if prefill value (from flexform)
		elseif ($options[$index]['selected']) {
			$selected = TRUE;
		}

		// if prefill value (from typoscript)
		elseif (isset($this->settings['prefill.'][$marker]) || isset($this->settings['prefill.'][$marker . '.'])) {
			if (isset($this->settings['prefill.'][$marker . '.']) && is_array($this->settings['prefill.'][$marker . '.'])) {
				$this->contentObjectRenderer->start(ObjectAccess::getGettableProperties($field));

				if (isset($this->settings['prefill.'][$marker . '.']['0'])) {

					/**
					 * plugin.tx_powermail.settings.setup.prefill.marker.0 = TEXT
					 * plugin.tx_powermail.settings.setup.prefill.marker.0.value = red
					 */
					foreach (array_keys($this->settings['prefill.'][$marker . '.']) as $key) {
						if (stristr($key, '.')) {
							continue;
						}
						$prefill = $this->contentObjectRenderer->cObjGetSingle(
							$this->settings['prefill.'][$marker . '.'][$key],
							$this->settings['prefill.'][$marker . '.'][$key . '.']
						);
						if ($prefill === $options[$index]['value'] || $prefill === $options[$index]['label']) {
							$selected = TRUE;
						}
					}
				} else {

					/**
					 * plugin.tx_powermail.settings.setup.prefill.marker = TEXT
					 * plugin.tx_powermail.settings.setup.prefill.marker.value = red
					 */
					$prefill = $this->contentObjectRenderer->cObjGetSingle(
						$this->settings['prefill.'][$marker],
						$this->settings['prefill.'][$marker . '.']
					);
					if ($prefill === $options[$index]['value'] || $prefill === $options[$index]['label']) {
						$selected = TRUE;
					}
				}
			} else {

				/**
				 * plugin.tx_powermail.settings.setup.prefill.marker = red
				 */
				if ($this->settings['prefill.'][$marker] === $options[$index]['value'] ||
					$this->settings['prefill.'][$marker] === $options[$index]['label']) {
					$selected = TRUE;
				}
			}

		}

		return $selected;
	}

	/**
	 * Check if form is cached
	 *
	 * @return bool
	 */
	protected function isCachedForm() {
		return Configuration::isEnableCachingActive();
	}

	/**
	 * Init
	 *
	 * @return void
	 */
	public function initialize() {
		$this->piVars = GeneralUtility::_GP('tx_powermail_pi1');
		$this->contentObjectRenderer = $this->objectManager->get('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
		$typoScriptSetup = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
		);
		$this->settings = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
	}
}