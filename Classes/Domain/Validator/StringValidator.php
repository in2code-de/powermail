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
     */
    protected function validateMandatory(mixed $value): bool
    {
        return StringUtility::isNotEmpty($value);
    }

    /**
     * Test string if valid email
     */
    protected function validateEmail(string $value): bool
    {
        return GeneralUtility::validEmail($value);
    }

    /**
     * Test string if its an URL
     */
    protected function validateUrl(string $value): bool
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
     */
    protected function validatePhone(string $value): bool
    {
        preg_match('/^(\+\d{1,4}|0+\d{1,5}|\(\d{1,5})[\d\s\/\(\)-]*\d+$/', $value, $result);
        return isset($result[0]) && ($result[0] !== '' && $result[0] !== '0') && $result[0] === $value;
    }

    /**
     * Test string if there are only numbers
     */
    protected function validateNumbersOnly(string $value): bool
    {
        return (string)((int)$value) === $value;
    }

    /**
     * Test string if there are only letters
     */
    protected function validateLettersOnly(string $value): bool
    {
        return preg_replace('/[^a-zA-Z]/', '', $value) === $value;
    }

    /**
     * Test if number is greater than configuration
     *
     * @param string $configuration e.g. "4"
     */
    protected function validateMinNumber(string $value, string $configuration): bool
    {
        return $value >= $configuration;
    }

    /**
     * Test if number is less than configuration
     *
     * @param string $configuration e.g. "4"
     */
    protected function validateMaxNumber(string $value, string $configuration): bool
    {
        return (float)$value <= (float)$configuration;
    }

    /**
     * Test if number is in range
     *
     * @param string $configuration e.g. "1,6" or "6"
     */
    protected function validateRange(string $value, string $configuration): bool
    {
        $values = GeneralUtility::trimExplode(',', $configuration, true);
        if (!isset($values[0]) || (int)$values[0] <= 0) {
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
     * @param string $configuration e.g. "1,6" or "6"
     */
    protected function validateLength(string $value, string $configuration): bool
    {
        $values = GeneralUtility::trimExplode(',', $configuration, true);
        if (!isset($values[0]) || (int)$values[0] <= 0) {
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
     * @param string $configuration e.g. "https?://.+"
     */
    protected function validatePattern(string $value, string $configuration): bool
    {
        return preg_match('~' . $configuration . '~', $value) === 1;
    }

    /**
     * @param string $value
     */
    protected function isValid($value): void
    {
    }
}
