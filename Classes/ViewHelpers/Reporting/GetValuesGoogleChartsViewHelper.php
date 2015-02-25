<?php
namespace In2code\Powermail\ViewHelpers\Reporting;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper check if given value is array or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class GetValuesGoogleChartsViewHelper extends AbstractViewHelper {

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
	 * @param bool $urlEncode
	 * @return string "label1|label2|label3"
	 */
	public function render($answers, $fieldUidOrKey, $separator = ',', $urlEncode = TRUE) {
		$string = '';
		if (empty($answers[$fieldUidOrKey]) || !is_array($answers[$fieldUidOrKey])) {
			return $string;
		}

		foreach ($answers[$fieldUidOrKey] as $amount) {
			$amount = str_replace(array($this->notAllowedSign, $separator), '', $amount);
			$amount = htmlspecialchars($amount);
			$string .= $amount;
			$string .= $separator;
		}

		$string = substr($string, 0, -1);
		if ($urlEncode) {
			$string = urlencode($string);
		}
		return $string;
	}
}