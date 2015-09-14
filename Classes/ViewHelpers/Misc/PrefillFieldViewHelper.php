<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\SessionUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Utility\ConfigurationUtility;

/**
 * Prefill a field
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class PrefillFieldViewHelper extends AbstractViewHelper {

	/**
	 * @var string|array
	 */
	protected $value = NULL;

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
	 * Field
	 *
	 * @var Field $field
	 */
	protected $field = NULL;

	/**
	 * Mail
	 *
	 * @var Mail $mail
	 */
	protected $mail = NULL;

	/**
	 * Markername
	 *
	 * @var string
	 */
	protected $marker = '';

	/**
	 * Prefill fields from type
	 * 		input
	 * 		textarea
	 * 		select
	 * 		country
	 * 		file
	 * 		hidden
	 * 		date
	 * 		location
	 *
	 * @param Field $field
	 * @param Mail $mail To prefill in Edit Action
	 * @param int $cycle Cycle Number (1,2,3...) - if filled checkbox or radiobutton
	 * @param string $default Fallback value
	 * @todo remove param $cycle
	 * @return string|array Prefill field
	 */
	public function render(Field $field, Mail $mail = NULL, $cycle = 0, $default = '') {
		$this
			->setMarker($field->getMarker())
			->setField($field)
			->setMail($mail)
			->setValue($default);

		// stop prefilling for cached forms to prevent wrong values
		if (!$this->isCachedForm()) {
			if ($cycle === 0) {
				$this->buildValue();
			} else {
				// TODO remove $cycle completely in next minor version
				GeneralUtility::deprecationLog(
					'Method \In2code\Powermail\ViewHelpers\Misc\PrefillFieldViewHelper::render() was called from a ' .
					'template or a partial with attribute "cycle". This attribute will be removed in next ' .
					'minor version of powermail. Further use can lead to exceptions. Please remove this attribute ' .
					'from your template files.'
				);
			}
		}
		return $this->getValue();
	}

	/**
	 * Build value
	 *
	 * @return void
	 */
	protected function buildValue() {
		$value = $this->getFromMail();
		if (empty($value)) {
			$value = $this->getFromMarker();
		}
		if (empty($value)) {
			$value = $this->getFromRawMarker();
		}
		if (empty($value)) {
			$value = $this->getFromFieldUid();
		}
		if (empty($value)) {
			$value = $this->getFromOldPowermailFieldUid();
		}
		if (empty($value)) {
			$value = $this->getFromFrontendUser();
		}
		if (empty($value)) {
			$value = $this->getFromPrefillValue();
		}
		if (empty($value)) {
			$value = $this->getFromTypoScriptContentObject();
		}
		if (empty($value)) {
			$value = $this->getFromTypoScriptRaw();
		}
		if (empty($value)) {
			$value = $this->getFromSession();
		}
		$this->setValue($value);
	}

	/**
	 * Get value from existing answer for edit view
	 *
	 * @return string|array
	 */
	protected function getFromMail() {
		$value = '';
		if ($this->getMail() !== NULL && $this->getMail()->getAnswers()) {
			foreach ($this->getMail()->getAnswers() as $answer) {
				/** @var Answer $answer */
				if ($answer->getField() === $this->getField()) {
					return $answer->getValue();
				}
			}
		}
		return $value;
	}

	/**
	 * Get value from GET/POST param &tx_powermail_pi1[field][marker]
	 *
	 * @return string
	 */
	protected function getFromMarker() {
		$value = '';
		if (isset($this->piVars['field'][$this->getMarker()])) {
			$value = $this->piVars['field'][$this->getMarker()];
		}
		return $value;
	}

	/**
	 * Get value from GET/POST param &tx_powermail_pi1[marker]
	 *
	 * @return string
	 */
	protected function getFromRawMarker() {
		$value = '';
		if (isset($this->piVars[$this->getMarker()])) {
			$value = $this->piVars[$this->getMarker()];
		}
		return $value;
	}

	/**
	 * Get value from GET/POST param &tx_powermail_pi1[field][123]
	 *
	 * @return string
	 */
	protected function getFromFieldUid() {
		$value = '';
		if (isset($this->piVars['field'][$this->getField()->getUid()])) {
			$value = $this->piVars['field'][$this->getField()->getUid()];
		}
		return $value;
	}

	/**
	 * Get value from GET/POST param &tx_powermail_pi1[uid123]
	 *
	 * @return string
	 */
	protected function getFromOldPowermailFieldUid() {
		$value = '';
		if (isset($this->piVars['uid' . $this->getField()->getUid()])) {
			$value = $this->piVars['uid' . $this->getField()->getUid()];
		}
		return $value;
	}

	/**
	 * Get value from current logged in Frontend User
	 *
	 * @return string
	 */
	protected function getFromFrontendUser() {
		$value = '';
		if ($this->getField()->getFeuserValue()) {
			$value = FrontendUtility::getPropertyFromLoggedInFrontendUser($this->getField()->getFeuserValue());
		}
		return $value;
	}

	/**
	 * Get value from prefill value from field record
	 *
	 * @return string
	 */
	protected function getFromPrefillValue() {
		$value = '';
		if ($this->getField()->getPrefillValue()) {
			$value = $this->getField()->getPrefillValue();
		}
		return $value;
	}

	/**
	 * Get from TypoScript content object like
	 *
	 * 		# direct value
	 * 		plugin.tx_powermail.settings.setup.prefill.marker = TEXT
	 * 		plugin.tx_powermail.settings.setup.prefill.marker.value = red
	 *
	 * 		# multiple value
	 * 		plugin.tx_powermail.settings.setup.prefill.marker.0 = TEXT
	 * 		plugin.tx_powermail.settings.setup.prefill.marker.0.value = red
	 *
	 * @return array|string
	 */
	protected function getFromTypoScriptContentObject() {
		$value = '';
		if (
			isset($this->settings['prefill.'][$this->getMarker() . '.']) &&
			is_array($this->settings['prefill.'][$this->getMarker() . '.'])
		) {
			$this->contentObjectRenderer->start(ObjectAccess::getGettableProperties($this->getField()));
			// Multivalue
			if (isset($this->settings['prefill.'][$this->getMarker() . '.']['0'])) {
				$value = array();
				foreach (array_keys($this->settings['prefill.'][$this->getMarker() . '.']) as $key) {
					if (stristr($key, '.')) {
						continue;
					}
					$value[] = $this->contentObjectRenderer->cObjGetSingle(
						$this->settings['prefill.'][$this->getMarker() . '.'][$key],
						$this->settings['prefill.'][$this->getMarker() . '.'][$key . '.']
					);
				}
			} else {
				// Single value
				$value = $this->contentObjectRenderer->cObjGetSingle(
					$this->settings['prefill.'][$this->getMarker()],
					$this->settings['prefill.'][$this->getMarker() . '.']
				);
			}
		}
		return $value;
	}

	/**
	 * Get from raw TypoScript settings like
	 * 		plugin.tx_powermail.settings.setup.prefill.marker = red
	 *
	 * @return string
	 */
	protected function getFromTypoScriptRaw() {
		$value = '';
		if (!empty($this->settings['prefill.'][$this->getMarker()])) {
			$value = $this->settings['prefill.'][$this->getMarker()];
		}
		return $value;
	}

	/**
	 * Get value from session if defined in TypoScript
	 *
	 * @return string
	 */
	protected function getFromSession() {
		$value = '';
		$sessionValues = SessionUtility::getSessionValuesForPrefill($this->settings);
		if (count($sessionValues)) {
			foreach ($sessionValues as $marker => $valueInSession) {
				if ($this->getMarker() === $marker) {
					return $valueInSession;
				}
			}
		}
		return $value;
	}

	/**
	 * @return string|array
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param string|array $value
	 * @return PrefillFieldViewHelper
	 */
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * @return Field
	 */
	public function getField() {
		return $this->field;
	}

	/**
	 * @param Field $field
	 * @return PrefillFieldViewHelper
	 */
	public function setField($field) {
		$this->field = $field;
		return $this;
	}

	/**
	 * @return Mail
	 */
	public function getMail() {
		return $this->mail;
	}

	/**
	 * @param Mail $mail
	 * @return PrefillFieldViewHelper
	 */
	public function setMail($mail) {
		$this->mail = $mail;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMarker() {
		return $this->marker;
	}

	/**
	 * @param string $marker
	 * @return PrefillFieldViewHelper
	 */
	public function setMarker($marker) {
		$this->marker = $marker;
		return $this;
	}

	/**
	 * Check if form is cached
	 *
	 * @return bool
	 */
	protected function isCachedForm() {
		return ConfigurationUtility::isEnableCachingActive();
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