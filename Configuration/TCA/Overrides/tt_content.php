<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:powermail/Configuration/FlexForms/FlexformPi1.xml',
    'powermail_pi1'
);

ExtensionUtility::registerPlugin(
    'powermail',
    'Pi1',
    'LLL:EXT:powermail/Resources/Private/Language/locallang_mod.xlf:powermail_pi1.title',
    null,
    'powermail'
);

ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:powermail/Configuration/FlexForms/FlexformPi1.xml',
    'powermail_pi1'
);

$GLOBALS['TCA']['tt_content']['types']['powermail_pi1']['showitem'] = '
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
        --palette--;;general,
        --palette--;;headers,
    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
        pi_flexform,
    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
        --palette--;;frames,
        --palette--;;appearanceLinks,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
        --palette--;;language,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
        --palette--;;hidden,
        --palette--;;access,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
        categories,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
        rowDescription,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
';
$GLOBALS['TCA']['tt_content']['types']['powermail_pi1']['previewRenderer']
    = In2code\Powermail\Hook\PluginPreviewRenderer::class;

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['powermail_pi1'] = 'extension-powermail-main';

/**
 * Disable not needed fields in tt_content
 */
// $GLOBALS['TCA']['tt_content']['types']['powermail_pi1']['subtypes_excludelist']['powermail_pi1'] = 'select_key,pages,recursive';
