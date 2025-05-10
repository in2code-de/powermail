<?php

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Utility\ConfigurationUtility;

/**
 * Fieldtypes
 *        "0"
 *        input
 *        textarea
 */
$typeDefault = 'page, title, type, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.palette1;1, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.validation_title;2, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.prefill_title;32, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete;50, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete_additional;51, ' .
    '--palette--;Layout;43, ' .
    'description, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    'tabs.access, sys_language_uid, ' .
    'l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        radio
 *        check
 */
$typeSettings = 'page, title, type, settings, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.palette1;1, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.validation_title;21, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.prefill_title;33, ' .
    '--palette--;Layout;43, ' .
    'description, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    'tabs.access, sys_language_uid, ' .
    'l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        select
 */
$typeSettingsMultiple = 'page, title, type, settings, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.palette1;1, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.validation_title;21, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.prefill_title;33, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete;50, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete_additional;51, ' .
    '--palette--;Layout;41, ' .
    'description, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    'tabs.access, sys_language_uid, ' .
    'l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        submit
 *        reset
 */
$typeSmall = 'page, title, type, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;Layout;43, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
    'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        captcha
 *        location
 */
$typeSmallDescription = 'page, title, type, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;Layout;43, ' .
    'description, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete;50, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete_additional;51, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
    'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        hidden
 */
$typeSmallPrefill = 'page, title, type, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.prefill_title;31, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete;50, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete_additional;51, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
    'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        country
 */
$typeSmallPrefillDescription = 'page, title, type, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.validation_title;21, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.prefill_title;31, ' .
    '--palette--;Layout;43, ' .
    'description, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete;50, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete_additional;51, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
    'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        password
 */
$typeSmallMandatory = 'page, title, type, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.validation_title;21, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.prefill_title;34, ' .
    '--palette--;Layout;43, ' .
    'description, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete;50, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.palette.autocomplete_additional;51, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
    'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        text
 *        html
 */
$typeText = 'page, title, type, text, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;Layout;43, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
    'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        content_element
 */
$typeContent = 'page, title, type, content_element, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;Layout;43, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
    'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        file
 */
$typeFile = 'page, title, type, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.validation_title;21, ' .
    '--palette--;Layout;41, ' .
    'description, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    'tabs.access, sys_language_uid, ' .
    'l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        date
 */
$typeDate = 'page, title, type, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.validation_title;21, ' .
    '--palette--;Layout;42, ' .
    'description, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    'tabs.access, sys_language_uid, ' .
    'l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        typoscript
 */
$typeTypoScript = 'page, title, type, path, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;Layout;43, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
    Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
    'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

$fieldsTca = [
    'ctrl' => [
        'title' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME,
        'label' => 'title',
        'type' => 'type',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'sortby' => 'sorting',
        'default_sortby' => 'ORDER BY sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'iconfile' => ConfigurationUtility::getIconPath(Field::TABLE_NAME . '.gif'),
        'searchFields' => 'title',
    ],
    'interface' => [
    ],
    'palettes' => [
        '1' => [
            'showitem' => 'sender_email, sender_name',
        ],
        '2' => [
            'showitem' => 'mandatory, mandatory_text, validation, validation_configuration',
        ],
        '21' => [
            'showitem' => 'mandatory, mandatory_text',
        ],
        '3' => [
            'showitem' => 'prefill_value, placeholder, feuser_value, create_from_typoscript',
        ],
        '31' => [
            'showitem' => 'prefill_value, feuser_value',
        ],
        '32' => [
            'showitem' => 'prefill_value, placeholder, feuser_value',
        ],
        '33' => [
            'showitem' => 'feuser_value, create_from_typoscript',
        ],
        '34' => [
            'showitem' => 'placeholder, placeholder_repeat',
        ],
        '4' => [
            'showitem' => 'css, multiselect, datepicker_settings',
        ],
        '41' => [
            'showitem' => 'css, multiselect',
        ],
        '42' => [
            'showitem' => 'css, datepicker_settings',
        ],
        '43' => [
            'showitem' => 'css',
        ],
        '5' => [
            'showitem' => 'auto_marker, marker, own_marker_select',
        ],
        '50' => [
            'showitem' => 'autocomplete_token,autocomplete_purpose',
        ],
        '51' => [
            'showitem' => 'autocomplete_section,autocomplete_type',
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => $typeDefault,
        ],
        'input' => [
            'showitem' => $typeDefault,
        ],
        'textarea' => [
            'showitem' => $typeDefault,
        ],
        'select' => [
            'showitem' => $typeSettingsMultiple,
        ],
        'check' => [
            'showitem' => $typeSettings,
        ],
        'radio' => [
            'showitem' => $typeSettings,
        ],
        'submit' => [
            'showitem' => $typeSmall,
        ],
        'captcha' => [
            'showitem' => $typeSmallDescription,
        ],
        'reset' => [
            'showitem' => $typeSmall,
        ],
        'text' => [
            'showitem' => $typeText,
        ],
        'content' => [
            'showitem' => $typeContent,
        ],
        'html' => [
            'showitem' => $typeText,
        ],
        'password' => [
            'showitem' => $typeSmallMandatory,
        ],
        'file' => [
            'showitem' => $typeFile,
        ],
        'hidden' => [
            'showitem' => $typeSmallPrefill,
        ],
        'date' => [
            'showitem' => $typeDate,
        ],
        'country' => [
            'showitem' => $typeSmallPrefillDescription,
        ],
        'location' => [
            'showitem' => $typeSmallDescription,
        ],
        'typoscript' => [
            'showitem' => $typeTypoScript,
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                [
                    'label' => '',
                    'value' => 0,
                ],
                'foreign_table' => Field::TABLE_NAME,
                'foreign_table_where' => 'AND ' . Field::TABLE_NAME . '.pid=###CURRENT_PID### AND ' .
                    Field::TABLE_NAME . '.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
        ],
        'title' => [
            'exclude' => false,
            'label' =>
                'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'type' => [
            'l10n_mode' => 'exclude',
            'exclude' => false,
            'label' =>
                'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.spacer1',
                        'value' => '--div--',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.0',
                        'value' => 'input',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.1',
                        'value' => 'textarea',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.2',
                        'value' => 'select',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.3',
                        'value' => 'check',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.4',
                        'value' => 'radio',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.5',
                        'value' => 'submit',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.spacer2',
                        'value' => '--div--',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.6',
                        'value' => 'captcha',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.7',
                        'value' => 'reset',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.8',
                        'value' => 'text',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.9',
                        'value' => 'content',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.10',
                        'value' => 'html',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.11',
                        'value' => 'password',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.12',
                        'value' => 'file',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.13',
                        'value' => 'hidden',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.14',
                        'value' => 'date',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.17',
                        'value' => 'country',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.15',
                        'value' => 'location',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.16',
                        'value' => 'typoscript',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.spacer3',
                        'value' => '--div--',
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'itemsProcFunc' => 'In2code\Powermail\Tca\AddOptionsToSelection->addOptionsForType',
                'required' => true,
            ],
        ],
        'settings' => [
            'exclude' => false,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.settings',
            'config' => [
                'type' => 'text',
                'cols' => '32',
                'rows' => '5',
                'default' => '',
            ],
        ],
        'path' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.path',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'content_element' => [
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.content_element',
            'config' => [
                'type' => 'group',
                'allowed' => 'tt_content',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'default' => 0,
            ],
        ],
        'text' => [
            'exclude' => false,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.text',
            'config' => [
                'type' => 'text',
                'cols' => '32',
                'rows' => '5',
                'default' => '',
            ],
        ],
        'sender_email' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.sender_email',
            'config' => [
                'type' => 'check',
            ],
        ],
        'sender_name' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.sender_name',
            'config' => [
                'type' => 'check',
            ],
        ],
        'mandatory' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'onChange' => 'reload',
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.mandatory',
            'config' => [
                'type' => 'check',
            ],
        ],
        'mandatory_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.mandatory_text',
            'config' => [
                'type' => 'text',
                'cols' => '26',
                'rows' => '1',
                'default' => '',
                'eval' => 'trim',
            ],
            'displayCond' => 'FIELD:mandatory:REQ:true',
        ],
        'validation' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'onChange' => 'reload',
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.validation',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:pleaseChoose',
                        'value' => 0,
                    ],
                    /**
                     * Spacer
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.spacer1',
                        'value' => '--div--',
                    ],
                    /**
                     * EMAIL (+html5)
                     *
                     * html5 example: <input type="email" />
                     * javascript example: <input type="text" data-powermail-type="email" />
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.validation.1',
                        'value' => 1,
                    ],
                    /**
                     * URL (+html5)
                     *
                     * html5 example: <input type="url" />
                     * javascript example: <input type="text" data-powermail-type="url" />
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.validation.2',
                        'value' => 2,
                    ],
                    /**
                     * PHONE (+html5)
                     *
                     * html5 example:
                     *        <input type="text" pattern="[\+]\d{2}[\(]\d{2}[\)]\d{4}[\-]\d{4}" />
                     * javascript example:
                     *        <input ... data-powermail-pattern="[\+]\d{2}[\(]\d{2}[\)]\d{4}[\-]\d{4}" />
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.validation.3',
                        'value' => 3,
                    ],
                    /**
                     * NUMBER/INTEGER (+html5)
                     *
                     * html5 example: <input type="number" />
                     * javascript example: <input type="text" data-powermail-type="integer" />
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.validation.4',
                        'value' => 4,
                    ],
                    /**
                     * LETTERS (+html5)
                     *
                     * html5 example: <input type="text" pattern="[a-zA-Z]*" />
                     * javascript example: <input type="text" data-powermail-pattern="[a-zA-Z]*" />
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.validation.5',
                        'value' => 5,
                    ],
                    /**
                     * Spacer
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.spacer2',
                        'value' => '--div--',
                    ],
                    /**
                     * MIN NUMBER (+html5)
                     *
                     * Note: Field validation_configuration for editors viewable
                     * html5 example: <input type="text" min="6" />
                     * javascript example: <input type="text" data-powermail-min="6" />
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.validation.6',
                        'value' => 6,
                    ],
                    /**
                     * MAX NUMBER (+html5)
                     *
                     * Note: Field validation_configuration for editors viewable
                     * html5 example: <input type="text" max="12" />
                     * javascript example: <input type="text" data-powermail-max="12" />
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.validation.7',
                        'value' => 7,
                    ],
                    /**
                     * RANGE (+html5)
                     *
                     * Note: Field validation_configuration for editors viewable
                     * html5 example: <input type="range" min="1" max="10" />
                     * javascript example:
                     *        <input type="text" data-powermail-type="range" min="1" max="10" />
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.validation.8',
                        'value' => 8,
                    ],
                    /**
                     * LENGTH
                     *
                     * Note: Field validation_configuration for editors viewable
                     * javascript example:
                     *        <input type="text" data-powermail-length="[6, 10]" />
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.validation.9',
                        'value' => 9,
                    ],
                    /**
                     * PATTERN (+html5)
                     *
                     * Note: Field validation_configuration for editors viewable
                     * html5 example: <input type="text" pattern="https?://.+" />
                     * javascript example:
                     *        <input type="text" data-powermail-pattern="https?://.+" />
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.validation.10',
                        'value' => 10,
                    ],
                    /**
                     * Spacer
                     */
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.type.spacer3',
                        'value' => '--div--',
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'default' => 0,
                'itemsProcFunc' => 'In2code\Powermail\Tca\AddOptionsToSelection->addOptionsForValidation',
            ],
        ],
        'validation_configuration' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.validationConfiguration',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
            'displayCond' => 'FIELD:validation:>:5',
        ],
        'prefill_value' => [
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.prefill_value',
            'config' => [
                'type' => 'text',
                'cols' => '26',
                'rows' => '2',
                'default' => '',
            ],
        ],
        'placeholder' => [
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.placeholder',
            'config' => [
                'type' => 'text',
                'cols' => '26',
                'rows' => '2',
                'default' => '',
            ],
        ],
        'placeholder_repeat' => [
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.placeholder_repeat',
            'config' => [
                'type' => 'text',
                'cols' => '26',
                'rows' => '2',
                'default' => '',
            ],
        ],
        'feuser_value' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.feuser_value',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:pleaseChoose',
                        'value' => '',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.feuser_value.name',
                        'value' => 'name',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.feuser_value.address',
                        'value' => 'address',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.feuser_value.phone',
                        'value' => 'telephone',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.feuser_value.fax',
                        'value' => 'fax',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.feuser_value.email',
                        'value' => 'email',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.feuser_value.zip',
                        'value' => 'zip',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.feuser_value.city',
                        'value' => 'city',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.feuser_value.country',
                        'value' => 'country',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.feuser_value.www',
                        'value' => 'www',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.feuser_value.company',
                        'value' => 'company',
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => '',
                'itemsProcFunc' => 'In2code\Powermail\Tca\AddOptionsToSelection->addOptionsForFeUserProperty',
            ],
        ],
        'create_from_typoscript' => [
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.create_from_typoscript',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => '',
            ],
        ],
        'css' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.css',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:pleaseChoose',
                        'value' => '',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.css.1',
                        'value' => 'layout1',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.css.2',
                        'value' => 'layout2',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.css.3',
                        'value' => 'layout3',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.css.4',
                        'value' => 'nolabel',
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => '',
            ],
        ],
        'multiselect' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.multiselect',
            'config' => [
                'type' => 'check',
            ],
        ],
        'datepicker_settings' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.datepicker_settings',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.datepicker_settings.1',
                        'value' => 'date',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.datepicker_settings.2',
                        'value' => 'datetime',
                    ],
                    [
                        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                            Field::TABLE_NAME . '.datepicker_settings.3',
                        'value' => 'time',
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => '',
            ],
        ],
        'auto_marker' => [
            // show marker in Backend record {markername}
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.auto_marker',
            'config' => [
                'type' => 'user',
                'renderType' => 'powermailMarker',
                'parameters' => [
                ],
            ],
            'displayCond' => 'FIELD:own_marker_select:REQ:false',
        ],
        'marker' => [
            // field that stores the marker
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.own_marker',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,alphanum_x,lower,nospace',
            ],
            'displayCond' => 'FIELD:own_marker_select:REQ:true',
        ],
        'own_marker_select' => [
            // checkbox to edit a marker
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'onChange' => 'reload',
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.own_marker_select',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.description',
            'config' => [
                'type' => 'text',
                'cols' => '26',
                'rows' => '2',
                'eval' => 'trim',
            ],
        ],
        'page' => [
            'exclude' => true,
            'displayCond' => 'FIELD:sys_language_uid:<=:0',
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.pages',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => '',
                        'value' => 0,
                    ],
                ],
                'foreign_table' => Page::TABLE_NAME,
                'foreign_table_where' => 'AND ' . Page::TABLE_NAME . '.pid=###CURRENT_PID### ' .
                    'AND ' . Page::TABLE_NAME . '.sys_language_uid IN (-1,###REC_FIELD_sys_language_uid###)',
                'default' => 0,
            ],
        ],
        'sorting' => [
            'config' => [
                'type' => 'none',
            ],
        ],
        'autocomplete_section' => [
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.autocomplete_section',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'max' => '100',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
            'displayCond' => [
                'AND' => [
                    'FIELD:type:IN:input,textarea,password,select,country,location,hidden',
                    'FIELD:autocomplete_token:!IN:on,off,nickname,sex,impp,url,organization-title,username,new-password,current-password,one-time-code,bday,bday-day,bday-month,bday-year,language,photo',
                    'FIELD:autocomplete_token:REQ:true',
                ],
            ],
        ],
        'autocomplete_type' => [
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.autocomplete_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '', 'value' => ''],
                    ['label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_type.billing', 'value' => 'billing'],
                    ['label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_type.shipping', 'value' => 'shipping'],
                ],
            ],
            'displayCond' => [
                'AND' => [
                    'FIELD:type:IN:input,textarea,select,country,location,hidden',
                    'FIELD:autocomplete_token:!IN:on,off,nickname,sex,impp,url,organization-title,tel-country-code,tel-area-code,tel-national,tel-local,tel-local-prefix,tel-local-suffix,tel-extension,username,new-password,current-password,one-time-code,bday,bday-day,bday-month,bday-year,language,photo',
                    'FIELD:autocomplete_token:REQ:true',
                ],
            ],
        ],
        'autocomplete_purpose' => [
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.autocomplete_purpose',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '', 'value' => ''],
                    ['label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_purpose.home', 'value' => 'home'],
                    ['label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_purpose.work', 'value' => 'work'],
                    ['label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_purpose.mobile', 'value' => 'mobile'],
                    ['label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_purpose.fax', 'value' => 'fax'],
                    ['label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_purpose.pager', 'value' => 'pager'],
                ],

            ],
            'displayCond' => [
                'AND' => [
                    'FIELD:type:IN:input,textarea,select,country,location,hidden',
                    'FIELD:autocomplete_token:IN:email,impp,tel',
                ],
            ],
        ],
        'autocomplete_token' => [
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.autocomplete_token',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => 'In2code\Powermail\Tca\AddAutocompleteTokens->getAutocompleteTokens',
                'itemsProcConfig' => [
                    'useDefaultItems' => true,
                ],
                'items' => [
                    ['label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.none', 'value' => ''],
                    ['label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.on', 'value' => 'on', 'icon' => '', 'group' => 'general'],
                    ['label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.off', 'value' => 'off', 'icon' => '', 'group' => 'general'],
                ],
                'itemGroups' => [
                    'general' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.groups.general',
                    'name' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.groups.name',
                    'contact' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.groups.contact',
                    'address' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.groups.address',
                    'tel' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.groups.tel',
                    'bday' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.groups.bday',
                    'user' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.groups.user',
                    'cc' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.groups.cc',
                    'other' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.groups.other',
                ],
                'default' => '',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
            'displayCond' => 'FIELD:type:IN:' . $fieldsWithAutocompleteOptions,
        ],
    ],
];
return $fieldsTca;
