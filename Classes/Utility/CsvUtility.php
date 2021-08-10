<?php
declare(strict_types = 1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Exception\OutdatedTypo3Exception;
use TYPO3\CMS\Core\Utility\CsvUtility as CsvUtilityCore;

/**
 * CsvUtility
 */
class CsvUtility extends CsvUtilityCore
{
    /**
     * See See https://typo3.org/security/advisory/typo3-psa-2021-002 for details
     *
     * @param string $value
     * @return string
     * @throws OutdatedTypo3Exception
     */
    public static function sanitizeCell(string $value): string
    {
        if (method_exists(__CLASS__, 'prefixControlLiterals')) {
            return self::prefixControlLiterals($value);
        }
        throw new OutdatedTypo3Exception(
            'Function prefixControlLiterals() does not exists in your TYPO3 instance. ' .
                'Please update to the latest version.',
            1628632343
        );
    }
}
