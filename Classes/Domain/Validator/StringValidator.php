<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class StringValidator
 */
class StringValidator extends AbstractValidator
{

    /**
     * Mandatory Check
     *
     * @param mixed $value
     * @return bool
     */
    protected function validateMandatory($value)
    {
        return StringUtility::isNotEmpty($value);
    }

    /**
     * Test string if valid email
     *
     * @param string $value
     * @return bool
     */
    protected function validateEmail($value)
    {
        return GeneralUtility::validEmail($value);
    }

    /**
     * Test string if its an URL
     *
     * @param string $value
     * @return bool
     */
    protected function validateUrl($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Test string if its a phone number
     *        01234567890
     *        0123 4567890
     *        0123 456 789
     *        (0123) 45678 - 90
     *        0012 345 678 9012
     *        0012 (0)345 / 67890 - 12
     *        +123456789012
     *        +12 345 678 9012
     *        +12 3456 7890123
     *        +49 (0) 123 3456789
     *        +49 (0)123 / 34567 - 89
     *
     * @param string $value
     * @return bool
     */
    protected function validatePhone($value)
    {
        preg_match('/^(\+\d{1,4}|0+\d{1,5}|\(\d{1,5})[\d\s\/\(\)-]*\d+$/', (string)$value, $result);
        return !empty($result[0]) && $result[0] === $value;
    }

    /**
     * Test string if there are only numbers
     *
     * @param string $value
     * @return bool
     */
    protected function validateNumbersOnly($value)
    {
        return strval((int)$value) === strval($value);
    }

    /**
     * Test string if there are only letters
     *
     * @param string $value
     * @return bool
     */
    protected function validateLettersOnly($value)
    {
        return preg_replace('/[^a-zA-Z]/', '', $value) === $value;
    }

    /**
     * Test if number is greater than configuration
     *
     * @param string $value
     * @param string $configuration e.g. "4"
     * @return bool
     */
    protected function validateMinNumber($value, $configuration)
    {
        return $value >= $configuration;
    }

    /**
     * Test if number is less than configuration
     *
     * @param string $value
     * @param string $configuration e.g. "4"
     * @return bool
     */
    protected function validateMaxNumber($value, $configuration)
    {
        return floatval($value) <= floatval($configuration);
    }

    /**
     * Test if number is in range
     *
     * @param string $value
     * @param string $configuration e.g. "1,6" or "6"
     * @return bool
     */
    protected function validateRange($value, $configuration)
    {
        $values = GeneralUtility::trimExplode(',', $configuration, true);
        if ((int)$values[0] <= 0) {
            return true;
        }
        if (!isset($values[1])) {
            $values[1] = $values[0];
            $values[0] = 1;
        }
        return $value >= $values[0] && $value <= $values[1];
    }

    /**
     * Test if stringlength is in range
     *
     * @param string $value
     * @param string $configuration e.g. "1,6" or "6"
     * @return bool
     */
    protected function validateLength($value, $configuration)
    {
        $values = GeneralUtility::trimExplode(',', $configuration, true);
        if ((int)$values[0] <= 0) {
            return true;
        }
        if (!isset($values[1])) {
            $values[1] = $values[0];
            $values[0] = 1;
        }
        return StringUtility::getStringLength($value) >= $values[0]
            && StringUtility::getStringLength($value) <= $values[1];
    }

    /**
     * Test if value is ok with RegEx
     *
     * @param string $value
     * @param string $configuration e.g. "https?://.+"
     * @return bool
     */
    protected function validatePattern($value, $configuration)
    {
        return preg_match('~' . $configuration . '~', (string)$value) === 1;
    }

    /**
     * @param string $value
     * @return void
     */
    public function isValid($value)
    {
        parent::isValid($value);
    }
}
