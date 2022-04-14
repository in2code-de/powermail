<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\String;

use In2code\Powermail\Utility\CsvUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * SanitizeCsvCellViewHelper
 * Because of a hijacking possibilit of Excel, we should clean the column value
 * See https://typo3.org/security/advisory/typo3-psa-2021-002 and https://owasp.org/www-community/attacks/CSV_Injection
 * for details
 */
class SanitizeCsvCellViewHelper extends AbstractViewHelper
{
    /**
     * @return string
     */
    public function render(): string
    {
        return CsvUtility::sanitizeCell((string)$this->renderChildren());
    }
}
