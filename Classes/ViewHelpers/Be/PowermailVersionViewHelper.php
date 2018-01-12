<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class PowermailVersionViewHelper
 */
class PowermailVersionViewHelper extends AbstractViewHelper
{

    /**
     * @return string
     */
    public function render(): string
    {
        return ExtensionManagementUtility::getExtensionVersion('powermail');
    }
}
