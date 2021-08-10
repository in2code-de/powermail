<?php
declare(strict_types = 1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility as LocalizationUtilityExtbase;

/**
 * Class LocalizationUtility
 */
class LocalizationUtility
{

    /**
     * Translate function with predefined extensionName
     * Could also be used together with unit tests
     *
     * @param string $key
     * @param string $extensionName
     * @param array|null $arguments
     * @return string
     */
    public static function translate(string $key, string $extensionName = 'powermail', array $arguments = null): string
    {
        if (ConfigurationUtility::isDatabaseConnectionAvailable() === false) {
            if (stristr((string)$key, 'datepicker_format')) {
                return 'Y-m-d H:i';
            }
            return (string)$key;
        }
        // @codeCoverageIgnoreStart
        return (string)LocalizationUtilityExtbase::translate($key, $extensionName, $arguments);
    }
}
