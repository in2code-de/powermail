<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

/**
 * Include Plugins
 */
ExtensionUtility::registerPlugin('Powermail', 'Pi1', 'Powermail');
ExtensionUtility::registerPlugin('Powermail', 'Pi2', 'Powermail_Frontend');

/**
 * Disable not needed fields in tt_content
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['powermail_pi1'] = 'select_key,pages,recursive';

/**
 * Include Flexform
 */
// Pi1
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['powermail_pi1'] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue(
    'powermail_pi1',
    'FILE:EXT:powermail/Configuration/FlexForms/FlexformPi1.xml'
);

// Pi2
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['powermail_pi2'] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue(
    'powermail_pi2',
    'FILE:EXT:powermail/Configuration/FlexForms/FlexformPi2.xml'
);
