<?php
use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

/**
 * Include TypoScript
 */
// @extensionScannerIgnoreLine seems to be a false positive
ExtensionManagementUtility::addStaticFile(
    'powermail',
    'Configuration/TypoScript/Main',
    'Main Template'
);
// @extensionScannerIgnoreLine seems to be a false positive
ExtensionManagementUtility::addStaticFile(
    'powermail',
    'Configuration/TypoScript/Powermail_Frontend',
    'Powermail_Frontend'
);
// @extensionScannerIgnoreLine seems to be a false positive
ExtensionManagementUtility::addStaticFile(
    'powermail',
    'Configuration/TypoScript/BootstrapClassesAndLayout',
    'Add classes and CSS based on bootstrap'
);
if (!ConfigurationUtility::isDisableMarketingInformationActive()) {
    // @extensionScannerIgnoreLine seems to be a false positive
    ExtensionManagementUtility::addStaticFile(
        'powermail',
        'Configuration/TypoScript/Marketing',
        'Marketing Information'
    );
}
