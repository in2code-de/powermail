<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use In2code\Powermail\Domain\Model\Answer;

/**
 * View helper check if value should be returned or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsNotExcludedFromPowermailAllViewHelper extends AbstractViewHelper {

	/**
	 * @var array
	 */
	protected $typeToTypoScriptType = array(
		'createAction' => 'submitPage',
		'confirmationAction' => 'confirmationPage',
		'sender' => 'senderMail',
		'receiver' => 'receiverMail',
		'optin' => 'optinMail'
	);

	/**
	 * View helper check if value should be returned or not
	 *
	 * @param \In2code\Powermail\Domain\Model\Answer $answer
	 * @param string $type "createAction", "confirmationAction", "sender", "receiver"
	 * @param array $settings
	 * @return bool
	 */
	public function render(Answer $answer, $type, $settings = array()) {
		// excludeFromFieldTypes
		if (
			$answer->getField() &&
			in_array($answer->getField()->getType(), $this->getExcludedValues($type, $settings, 'excludeFromFieldTypes'))
		) {
			return FALSE;
		}

		// excludeFromMarkerNames
		if (
			$answer->getField() &&
			in_array($answer->getField()->getMarker(), $this->getExcludedValues($type, $settings, 'excludeFromMarkerNames'))
		) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Return markers from TypoScript
	 * 		plugin.tx_powermail.settings.setup.excludeFromPowermailAllMarker {
	 * 			submitPage.excludeFromMarkerNames = marker1, marker2
	 * 			submitPage.excludeFromFieldTypes = marker1, marker2
	 * 		}
	 *
	 * @param string $type
	 * @param array $settings
	 * @param string $configurationType
	 * @return array
	 */
	protected function getExcludedValues($type, $settings, $configurationType = 'excludeFromFieldTypes') {
		if (!empty($settings['excludeFromPowermailAllMarker'][$this->typeToTypoScriptType[$type]][$configurationType])) {
			return GeneralUtility::trimExplode(
				',',
				$settings['excludeFromPowermailAllMarker'][$this->typeToTypoScriptType[$type]][$configurationType],
				TRUE
			);
		}
		return array();
	}
}