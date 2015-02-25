<?php
namespace In2code\Powermail\ViewHelpers\Reporting;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper check if given value is array or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class GetLabelsGoogleChartsViewHelper extends AbstractViewHelper {

	/**
	 * Not allowed sign
	 */
	protected $notAllowedSign = '"';

	/**
	 * View helper check if given value is array or not
	 *
	 * @param array $answers Grouped Answers
	 * @param string $fieldUidOrKey
	 * @param string $separator
	 * @param int $crop Crop each label after X signs
	 * @param string $append append after crop
	 * @param bool $urlEncode
	 * @return string "label1|label2|label3"
	 */
	public function render($answers, $fieldUidOrKey, $separator = '|', $crop = 15, $append = '...', $urlEncode = TRUE) {
		$string = '';
		if (empty($answers[$fieldUidOrKey]) || !is_array($answers[$fieldUidOrKey])) {
			return $string;
		}

		foreach (array_keys($answers[$fieldUidOrKey]) as $value) {
			$value = str_replace(array($this->notAllowedSign, $separator), '', $value);
			$value = htmlspecialchars($value);
			if (strlen($value) > $crop) {
				$value = substr($value, 0, $crop) . $append;
			}
			$string .= $value;
			$string .= $separator;
		}

		$string = substr($string, 0, -1);
		if ($urlEncode) {
			$string = urlencode($string);
		}
		return $string;
	}
}