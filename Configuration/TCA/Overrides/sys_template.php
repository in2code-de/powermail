<?php
defined('TYPO3_MODE') || die();

/**
 * Include TypoScript
 */
// @extensionScannerIgnoreLine seems to be a false positive
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'powermail',
    'Configuration/TypoScript/Main',
    'Main Template'
);
// @extensionScannerIgnoreLine seems to be a false positive
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'powermail',
    'Configuration/TypoScript/Powermail_Frontend',
    'Powermail_Frontend'
);
// @extensionScannerIgnoreLine seems to be a false positive
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'powermail',
    'Configuration/TypoScript/BootstrapClassesAndLayout',
    'Add classes and CSS based on bootstrap'
);
if (!\In2code\Powermail\Utility\ConfigurationUtility::isDisableMarketingInformationActive()) {
    // @extensionScannerIgnoreLine seems to be a false positive
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'powermail',
        'Configuration/TypoScript/Marketing',
        'Marketing Information'
    );
}
