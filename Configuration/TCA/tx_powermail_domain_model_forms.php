<?php
use In2code\Powermail\Utility\ConfigurationUtility;

$formsTca = [
    'ctrl' => [
        'title' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_forms',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY title ASC',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'iconfile' => ConfigurationUtility::getIconPath('tx_powermail_domain_model_forms.gif')
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, css, pages, note',
    ],
    'types' => [
        '1' => [
            'showitem' => 'title, pages, note, ' .
                '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                'tx_powermail_domain_model_fields.sheet1, ' .
                'css, --div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
                'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime'
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'default' => 0,
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0]
                ],
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_powermail_domain_model_forms',
                'foreign_table_where' => 'AND tx_powermail_domain_model_forms.pid=###CURRENT_PID### AND' .
                    ' tx_powermail_domain_model_forms.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ]
        ],
        'hidden' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
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
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'title' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                'tx_powermail_domain_model_forms.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'note' => [
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'user',
                'userFunc' => 'In2code\Powermail\Utility\Tca\ShowFormNoteIfNoEmailOrNameSelected->showNote'
            ],
        ],
        'css' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                'tx_powermail_domain_model_forms.css',
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
                        'tx_powermail_domain_model_forms.css.1',
                        'layout1'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        'tx_powermail_domain_model_forms.css.2',
                        'layout2'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        'tx_powermail_domain_model_forms.css.3',
                        'layout3'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        'tx_powermail_domain_model_forms.css.4',
                        'nolabel'
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => ''
            ],
        ],
        'pages' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                'tx_powermail_domain_model_forms.pages',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_powermail_domain_model_pages',
                'foreign_table_where' => 'AND tx_powermail_domain_model_pages.deleted = 1 ' .
                    'AND tx_powermail_domain_model_pages.hidden = 0 ' .
                    'and tx_powermail_domain_model_pages.sys_language_uid = 0',
                'foreign_field' => 'forms',
                'foreign_sortby' => 'sorting',
                'maxitems' => 1000,
                'appearance' => [
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                    'useSortable' => 1,
                    'newRecordLinkAddTitle' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 0,
                    'showAllLocalizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showRemovedLocalizationRecords' => 1,
                ],
                'behaviour' => [
                    'localizeChildrenAtParentLocalization' => 1,
                    'localizationMode' => 'select',
                ],
            ],
        ],
    ],
];

/**
 * Replace IRRE relation with element browser for page selection
 */
if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
    $formsTca['columns']['pages'] = [
        'l10n_mode' => 'exclude',
        'exclude' => 0,
        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
            'tx_powermail_domain_model_forms.pages',
        'config' => [
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'tx_powermail_domain_model_pages',
            'foreign_table' => 'tx_powermail_domain_model_pages',
            'minitems' => 1,
            'maxitems' => 100
        ],
    ];
}

/**
 * Switch from l10n_mode "exclude" to "mergeIfNotBlank"
 */
if (ConfigurationUtility::isL10nModeMergeActive()) {
    $formsTca['columns']['css']['l10n_mode'] = 'mergeIfNotBlank';
}

return $formsTca;
