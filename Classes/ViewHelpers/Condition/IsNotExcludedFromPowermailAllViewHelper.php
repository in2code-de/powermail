<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * View helper check if value should be returned or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsNotExcludedFromPowermailAllViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * View helper check if value should be returned or not
	 *
	 * @param \In2code\Powermail\Domain\Model\Answer $answer
	 * @param string $type "createAction", "confirmationAction", "sender", "receiver"
	 * @param array $settings
	 * @return bool
	 */
	public function render(\In2code\Powermail\Domain\Model\Answer $answer, $type, $settings = array()) {
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
	 * @param string $type
	 * @param array $settings
	 * @param string $configurationType
	 * @return array
	 */
	protected function getExcludedValues($type, $settings, $configurationType = 'excludeFromFieldTypes') {
		switch ($type) {

			// On Submitpage
			case 'createAction':
				if (!empty($settings['excludeFromPowermailAllMarker']['submitPage'][$configurationType])) {
					return GeneralUtility::trimExplode(
						',',
						$settings['excludeFromPowermailAllMarker']['submitPage'][$configurationType],
						TRUE
					);
				}
				break;

			// On Confirmationpage
			case 'confirmationAction':
				if (!empty($settings['excludeFromPowermailAllMarker']['confirmationPage'][$configurationType])) {
					return GeneralUtility::trimExplode(
						',',
						$settings['excludeFromPowermailAllMarker']['confirmationPage'][$configurationType],
						TRUE
					);
				}
				break;

			// In Mail to sender
			case 'sender':
				if (!empty($settings['excludeFromPowermailAllMarker']['senderMail'][$configurationType])) {
					return GeneralUtility::trimExplode(
						',',
						$settings['excludeFromPowermailAllMarker']['senderMail'][$configurationType],
						TRUE
					);
				}
				break;

			// In Mail to receiver
			case 'receiver':
				if (!empty($settings['excludeFromPowermailAllMarker']['receiverMail'][$configurationType])) {
					return GeneralUtility::trimExplode(
						',',
						$settings['excludeFromPowermailAllMarker']['receiverMail'][$configurationType],
						TRUE
					);
				}
				break;

			default:
		}
		return array();
	}
}