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
$typeDefault = 'pages, title, type, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.palette1;1, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.validation_title;2, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.prefill_title;32, ' .
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
$typeSettings = 'pages, title, type, settings, ' .
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
$typeSettingsMultiple = 'pages, title, type, settings, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.palette1;1, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.validation_title;21, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.prefill_title;33, ' .
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
$typeSmall = 'pages, title, type, ' .
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
$typeSmallDescription = 'pages, title, type, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;Layout;43, ' .
    'description, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
        'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        hidden
 */
$typeSmallPrefill = 'pages, title, type, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.prefill_title;31, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
    'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        country
 */
$typeSmallPrefillDescription = 'pages, title, type, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.validation_title;21, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.prefill_title;31, ' .
    '--palette--;Layout;43, ' .
    'description, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
    'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        password
 */
$typeSmallMandatory = 'pages, title, type, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.validation_title;21, ' .
    '--palette--;Layout;43, ' .
    'description, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
        Field::TABLE_NAME . '.marker_title;5, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
    'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime';

/**
 * Fieldtypes
 *        text
 *        html
 */
$typeText = 'pages, title, type, text, ' .
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
$typeContent = 'pages, title, type, content_element, ' .
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
$typeFile = 'pages, title, type, ' .
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
$typeDate = 'pages, title, type, ' .
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
$typeTypoScript = 'pages, title, type, path, ' .
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
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
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
        'iconfile' => ConfigurationUtility::getIconPath(Field::TABLE_NAME . '.gif')
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, type, settings, ' .
            'path, content_element, text, prefill_value, placeholder, create_from_typoscript, mandatory, ' .
            'validation, validation_configuration, css, description, multiselect, datepicker_settings, ' .
            'feuser_value, sender_email, sender_name, own_marker_select, auto_marker, marker',
    ],
    'palettes' => [
        '1' => [
            'showitem' => 'sender_email, sender_name',
            'canNotCollapse' => 1
        ],
        '2' => [
            'showitem' => 'mandatory, validation, validation_configuration',
            'canNotCollapse' => 1
        ],
        '21' => [
            'showitem' => 'mandatory',
            'canNotCollapse' => 1
        ],
        '3' => [
            'showitem' => 'prefill_value, placeholder, feuser_value, create_from_typoscript',
            'canNotCollapse' => 1
        ],
        '31' => [
            'showitem' => 'prefill_value, feuser_value',
            'canNotCollapse' => 1
        ],
        '32' => [
            'showitem' => 'prefill_value, placeholder, feuser_value',
            'canNotCollapse' => 1
        ],
        '33' => [
            'showitem' => 'feuser_value, create_from_typoscript',
            'canNotCollapse' => 1
        ],
        '4' => [
            'showitem' => 'css, multiselect, datepicker_settings'
        ],
        '41' => [
            'showitem' => 'css, multiselect'
        ],
        '42' => [
            'showitem' => 'css, datepicker_settings'
        ],
        '43' => [
            'showitem' => 'css'
        ],
        '5' => [
            'showitem' => 'auto_marker, marker, own_marker_select',
            'canNotCollapse' => 1
        ],
        'canNotCollapse' => '1'
    ],
    'types' => [
        '0' => [
            'showitem' => $typeDefault
        ],
        'input' => [
            'showitem' => $typeDefault
        ],
        'textarea' => [
            'showitem' => $typeDefault
        ],
        'select' => [
            'showitem' => $typeSettingsMultiple
        ],
        'check' => [
            'showitem' => $typeSettings
        ],
        'radio' => [
            'showitem' => $typeSettings
        ],
        'submit' => [
            'showitem' => $typeSmall
        ],
        'captcha' => [
            'showitem' => $typeSmallDescription
        ],
        'reset' => [
            'showitem' => $typeSmall
        ],
        'text' => [
            'showitem' => $typeText
        ],
        'content' => [
            'showitem' => $typeContent
        ],
        'html' => [
            'showitem' => $typeText
        ],
        'password' => [
            'showitem' => $typeSmallMandatory
        ],
        'file' => [
            'showitem' => $typeFile
        ],
        'hidden' => [
            'showitem' => $typeSmallPrefill
        ],
        'date' => [
            'showitem' => $typeDate
        ],
        'country' => [
            'showitem' => $typeSmallPrefillDescription
        ],
        'location' => [
            'showitem' => $typeSmallDescription
        ],
        'typoscript' => [
            'showitem' => $typeTypoScript
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'default' => 0,
                'items' => [
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xml:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xml:LGL.default_value', 0]
                ],
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => Field::TABLE_NAME,
                'foreign_table_where' => 'AND ' . Field::TABLE_NAME . '.pid=###CURRENT_PID### AND ' .
                    Field::TABLE_NAME . '.sys_language_uid IN (-1,0)',
                'default' => 0
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
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'size' => 13,
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'endtime' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'size' => 13,
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'title' => [
            'exclude' => 0,
            'label' =>
                'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'type' => [
            'l10n_mode' => 'exclude',
            'exclude' => 0,
            'label' =>
                'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.spacer1',
                        '--div--'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.0',
                        'input'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.1',
                        'textarea'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.2',
                        'select'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.3',
                        'check'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.4',
                        'radio'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.5',
                        'submit'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.spacer2',
                        '--div--'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.6',
                        'captcha'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.7',
                        'reset'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.8',
                        'text'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.9',
                        'content'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.10',
                        'html'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.11',
                        'password'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.12',
                        'file'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.13',
                        'hidden'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.14',
                        'date'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.17',
                        'country'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.15',
                        'location'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.16',
                        'typoscript'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.spacer3',
                        '--div--'
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'required',
                'itemsProcFunc' => 'In2code\Powermail\Tca\AddOptionsToSelection->addOptionsForType',
            ],
        ],
        'settings' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.settings',
            'config' => [
                'type' => 'text',
                'cols' => '32',
                'rows' => '5',
                'default' => ''
            ],
        ],
        'path' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.path',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'content_element' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.content_element',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tt_content',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'default' => 0
            ],
        ],
        'text' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.text',
            'config' => [
                'type' => 'text',
                'cols' => '32',
                'rows' => '5',
                'default' => ''
            ],
        ],
        'sender_email' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.sender_email',
            'config' => [
                'type' => 'check'
            ],
        ],
        'sender_name' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.sender_name',
            'config' => [
                'type' => 'check'
            ],
        ],
        'mandatory' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.mandatory',
            'config' => [
                'type' => 'check'
            ],
        ],
        'validation' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'onChange' => 'reload',
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.validation',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:pleaseChoose',
                        0
                    ],
                    /**
                     * Spacer
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.spacer1',
                        '--div--'
                    ],
                    /**
                     * EMAIL (+html5)
                     *
                     * html5 example: <input type="email" />
                     * javascript example: <input type="text" data-parsley-type="email" />
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.validation.1',
                        1
                    ],
                    /**
                     * URL (+html5)
                     *
                     * html5 example: <input type="url" />
                     * javascript example: <input type="text" data-parsley-type="url" />
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.validation.2',
                        2
                    ],
                    /**
                     * PHONE (+html5)
                     *
                     * html5 example:
                     *        <input type="text" pattern="[\+]\d{2}[\(]\d{2}[\)]\d{4}[\-]\d{4}" />
                     * javascript example:
                     *        <input ... data-parsley-pattern="[\+]\d{2}[\(]\d{2}[\)]\d{4}[\-]\d{4}" />
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.validation.3',
                        3
                    ],
                    /**
                     * NUMBER/INTEGER (+html5)
                     *
                     * html5 example: <input type="number" />
                     * javascript example: <input type="text" data-parsley-type="integer" />
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.validation.4',
                        4
                    ],
                    /**
                     * LETTERS (+html5)
                     *
                     * html5 example: <input type="text" pattern="[a-zA-Z]*" />
                     * javascript example: <input type="text" data-parsley-pattern="[a-zA-Z]*" />
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.validation.5',
                        5
                    ],
                    /**
                     * Spacer
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.spacer2',
                        '--div--'
                    ],
                    /**
                     * MIN NUMBER (+html5)
                     *
                     * Note: Field validation_configuration for editors viewable
                     * html5 example: <input type="text" min="6" />
                     * javascript example: <input type="text" data-parsley-min="6" />
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.validation.6',
                        6
                    ],
                    /**
                     * MAX NUMBER (+html5)
                     *
                     * Note: Field validation_configuration for editors viewable
                     * html5 example: <input type="text" max="12" />
                     * javascript example: <input type="text" data-parsley-max="12" />
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.validation.7',
                        7
                    ],
                    /**
                     * RANGE (+html5)
                     *
                     * Note: Field validation_configuration for editors viewable
                     * html5 example: <input type="range" min="1" max="10" />
                     * javascript example:
                     *        <input type="text" data-parsley-type="range" min="1" max="10" />
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.validation.8',
                        8
                    ],
                    /**
                     * LENGTH
                     *
                     * Note: Field validation_configuration for editors viewable
                     * javascript example:
                     *        <input type="text" data-parsley-length="[6, 10]" />
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.validation.9',
                        9
                    ],
                    /**
                     * PATTERN (+html5)
                     *
                     * Note: Field validation_configuration for editors viewable
                     * html5 example: <input type="text" pattern="https?://.+" />
                     * javascript example:
                     *        <input type="text" data-parsley-pattern="https?://.+" />
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.validation.10',
                        10
                    ],
                    /**
                     * Spacer
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.type.spacer3',
                        '--div--'
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
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.validationConfiguration',
            'config' => [
                'type' => 'input',
                'size' => 30
            ],
            'displayCond' => 'FIELD:validation:>:5'
        ],
        'prefill_value' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.prefill_value',
            'config' => [
                'type' => 'text',
                'cols' => '26',
                'rows' => '2',
                'default' => ''
            ],
        ],
        'placeholder' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.placeholder',
            'config' => [
                'type' => 'text',
                'cols' => '26',
                'rows' => '2',
                'default' => ''
            ],
        ],
        'feuser_value' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.feuser_value',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:pleaseChoose',
                        ''
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.feuser_value.name',
                        'name'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.feuser_value.address',
                        'address'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.feuser_value.phone',
                        'telephone'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.feuser_value.fax',
                        'fax'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.feuser_value.email',
                        'email'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.feuser_value.zip',
                        'zip'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.feuser_value.city',
                        'city'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.feuser_value.country',
                        'country'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.feuser_value.www',
                        'www'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.feuser_value.company',
                        'company'
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => '',
                'itemsProcFunc' => 'In2code\Powermail\Tca\AddOptionsToSelection->addOptionsForFeUserProperty',
            ],
        ],
        'create_from_typoscript' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.create_from_typoscript',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'css' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.css',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:pleaseChoose',
                        ''
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.css.1',
                        'layout1'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.css.2',
                        'layout2'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.css.3',
                        'layout3'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.css.4',
                        'nolabel'
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => ''
            ],
        ],
        'multiselect' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.multiselect',
            'config' => [
                'type' => 'check'
            ],
        ],
        'datepicker_settings' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.datepicker_settings',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.datepicker_settings.1',
                        'date'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.datepicker_settings.2',
                        'datetime'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Field::TABLE_NAME . '.datepicker_settings.3',
                        'time'
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => ''
            ],
        ],
        'auto_marker' => [
            // show marker in Backend record {markername}
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.auto_marker',
            'config' => [
                'type' => 'user',
                'userFunc' => 'In2code\Powermail\Tca\Marker->createMarker'
            ],
            'displayCond' => 'FIELD:own_marker_select:REQ:false'
        ],
        'marker' => [
            // field that stores the marker
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.own_marker',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,alphanum_x,lower,nospace'
            ],
            'displayCond' => 'FIELD:own_marker_select:REQ:true'
        ],
        'own_marker_select' => [
            // checkbox to edit a marker
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'onChange' => 'reload',
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.own_marker_select',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.description',
            'config' => [
                'type' => 'text',
                'cols' => '26',
                'rows' => '2',
                'eval' => 'trim'
            ],
        ],
        'pages' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:sys_language_uid:<=:0',
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.pages',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => Page::TABLE_NAME,
                'foreign_table_where' => 'AND ' . Page::TABLE_NAME . '.pid=###CURRENT_PID### ' .
                    'AND ' . Page::TABLE_NAME . '.sys_language_uid IN (-1,###REC_FIELD_sys_language_uid###)',
                'default' => 0
            ],
        ],
        'sorting' => [
            'config' => [
                'type' => 'none',
            ],
        ],
    ],
];

/**
 * Switch from l10n_mode "exclude" to "mergeIfNotBlank"
 */
if (ConfigurationUtility::isL10nModeMergeActive()) {
    $fieldsTca['columns']['path']['l10n_mode'] = 'mergeIfNotBlank';
    $fieldsTca['columns']['sender_email']['l10n_mode'] = 'mergeIfNotBlank';
    $fieldsTca['columns']['sender_name']['l10n_mode'] = 'mergeIfNotBlank';
    $fieldsTca['columns']['mandatory']['l10n_mode'] = 'mergeIfNotBlank';
    $fieldsTca['columns']['validation']['l10n_mode'] = 'mergeIfNotBlank';
    $fieldsTca['columns']['validation_configuration']['l10n_mode'] = 'mergeIfNotBlank';
    $fieldsTca['columns']['feuser_value']['l10n_mode'] = 'mergeIfNotBlank';
    $fieldsTca['columns']['css']['l10n_mode'] = 'mergeIfNotBlank';
    $fieldsTca['columns']['own_marker_select']['l10n_mode'] = 'mergeIfNotBlank';
    $fieldsTca['columns']['pages']['l10n_mode'] = 'mergeIfNotBlank';
}

return $fieldsTca;
