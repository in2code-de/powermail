<?php
namespace In2code\Powermail\ViewHelpers\Reporting;

/**
 * View helper check if given value is array or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class GetLabelsGoogleChartsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * View helper check if given value is array or not
	 *
	 * @param array $answers Grouped Answers
	 * @param string $fieldUidOrKey
	 * @param int $crop Crop each label after X signs
	 * @return string "label1|label2|label3"
	 */
	public function render($answers, $fieldUidOrKey, $crop = 15) {
		$string = '';
		if (!isset($answers[$fieldUidOrKey])) {
			return '';
		}

		// create string
		foreach ((array) $answers[$fieldUidOrKey] as $value => $amount) {
			$amount = NULL;
			if (strlen($value) > $crop) {
				$value = substr($value, 0, $crop) . '...';
			}
			$string .= $value;
			$string .= '|';
		}

		return urlencode(substr($string, 0, -1));
	}
}