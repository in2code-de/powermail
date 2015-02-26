<?php
namespace In2code\Powermail\Domain\Validator;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use In2code\Powermail\Utility\Div;

/**
 * StringValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class StringValidator extends AbstractValidator {

	/**
	 * Mandatory Check
	 *
	 * @param \mixed $value
	 * @return bool
	 */
	protected function validateMandatory($value) {
		return Div::isNotEmpty($value);
	}

	/**
	 * Test string if valid email
	 *
	 * @param \string $value
	 * @return bool
	 */
	protected function validateEmail($value) {
		return GeneralUtility::validEmail($value);
	}

	/**
	 * Test string if its an URL
	 *
	 * @param \string $value
	 * @return bool
	 */
	protected function validateUrl($value) {
		if (filter_var($value, FILTER_VALIDATE_URL) !== FALSE) {
			return TRUE;
		};
		return FALSE;
	}

	/**
	 * Test string if its a phone number
	 * 		0 123 456 7890
	 * 		0123 4567890
	 * 		01234567890
	 * 		+12 345 6789012
	 * 			see StringValidatorTest for all possibility
	 *
	 * @param \string $value
	 * @return bool
	 */
	protected function validatePhone($value) {
		preg_match('/^(\+\d{1,4}|0+\d{1,5}|\(\d{1,5})[\d\s\/\(\)-]*\d+$/', $value, $result);
		if (!empty($result[0]) && $result[0] === $value) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Test string if there are only numbers
	 *
	 * @param \string $value
	 * @return bool
	 */
	protected function validateNumbersOnly($value) {
		if (strval(intval($value)) === strval($value)) {
			return TRUE;
		};
		return FALSE;
	}

	/**
	 * Test string if there are only letters
	 *
	 * @param \string $value
	 * @return bool
	 */
	protected function validateLettersOnly($value) {
		if (preg_replace('/[^a-zA-Z]/', '', $value) === $value) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Test if number is greater than configuration
	 *
	 * @param \string $value
	 * @param \string $configuration e.g. "4"
	 * @return bool
	 */
	protected function validateMinNumber($value, $configuration) {
		if ($value >= $configuration) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Test if number is less than configuration
	 *
	 * @param \string $value
	 * @param \string $configuration e.g. "4"
	 * @return bool
	 */
	protected function validateMaxNumber($value, $configuration) {
		if (floatval($value) <= floatval($configuration)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Test if number is in range
	 *
	 * @param \string $value
	 * @param \string $configuration e.g. "1,6" or "6"
	 * @return bool
	 */
	protected function validateRange($value, $configuration) {
		$values = GeneralUtility::trimExplode(',', $configuration, TRUE);
		if (intval($values[0]) <= 0) {
			return TRUE;
		}
		if (!isset($values[1])) {
			$values[1] = $values[0];
			$values[0] = 1;
		}
		if ($value >= $values[0] && $value <= $values[1]) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Test if stringlength is in range
	 *
	 * @param \string $value
	 * @param \string $configuration e.g. "1,6" or "6"
	 * @return bool
	 */
	protected function validateLength($value, $configuration) {
		$values = GeneralUtility::trimExplode(',', $configuration, TRUE);
		if (intval($values[0]) <= 0) {
			return TRUE;
		}
		if (!isset($values[1])) {
			$values[1] = $values[0];
			$values[0] = 1;
		}
		if (strlen($value) >= $values[0] && strlen($value) <= $values[1]) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Test if value is ok with RegEx
	 *
	 * @param \string $value
	 * @param \string $configuration e.g. "https?://.+"
	 * @return bool
	 */
	protected function validatePattern($value, $configuration) {
		if (preg_match('~' . $configuration . '~', $value) === 1) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Must be there because of the interface
	 *
	 * @param \string $value
	 * @return void
	 */
	public function isValid($value) {
		parent::isValid($value);
	}
}