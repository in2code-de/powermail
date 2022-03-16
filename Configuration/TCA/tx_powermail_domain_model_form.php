<?php
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Utility\ConfigurationUtility;

$formsTca = [
    'ctrl' => [
        'title' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Form::TABLE_NAME,
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
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
        'iconfile' => ConfigurationUtility::getIconPath(Form::TABLE_NAME . '.gif')
    ],
    'interface' => [
    ],
    'types' => [
        '1' => [
            'showitem' => 'title, pages, note, ' .
                '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Field::TABLE_NAME . '.sheet1, ' .
                'css, --div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
                'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime'
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
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => Form::TABLE_NAME,
                'foreign_table_where' => 'AND ' . Form::TABLE_NAME . '.pid=###CURRENT_PID### AND ' .
                    Form::TABLE_NAME . '.sys_language_uid IN (-1,0)',
                'default' => 0
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ]
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
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0
            ],
        ],
        'endtime' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0
            ],
        ],
        'title' => [
            'exclude' => false,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Form::TABLE_NAME . '.title',
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
                'renderType' => 'powermailShowFormNoteIfNoEmailOrNameSelected',
                'parameters' => [
                ]
            ]
        ],
        'css' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Form::TABLE_NAME . '.css',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:pleaseChoose',
                        ''
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Form::TABLE_NAME . '.css.1',
                        'layout1'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Form::TABLE_NAME . '.css.2',
                        'layout2'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Form::TABLE_NAME . '.css.3',
                        'layout3'
                    ],
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Form::TABLE_NAME . '.css.4',
                        'nolabel'
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => ''
            ],
        ],
        'pages' => [
            'exclude' => false,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Form::TABLE_NAME . '.pages',
            'config' => [
                'type' => 'inline',
                'foreign_table' => Page::TABLE_NAME,
                'foreign_table_where' => 'AND ' . Page::TABLE_NAME . '.deleted = 1 ' .
                    'aND ' . Page::TABLE_NAME . '.hidden = 0 ' .
                    'and ' . Page::TABLE_NAME . '.sys_language_uid = 0',
                'foreign_field' => 'form',
                'foreign_sortby' => 'sorting',
                'maxitems' => 1000,
                'appearance' => [
                    'expandSingle' => 1,
                    'useSortable' => 1,
                    'newRecordLinkAddTitle' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 0,
                    'showAllLocalizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1
                ]
            ]
        ]
    ]
];

/**
 * Replace IRRE relation with element browser for page selection
 */
if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
    $formsTca['columns']['pages'] = [
        'l10n_mode' => 'exclude',
        'exclude' => false,
        'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Form::TABLE_NAME . '.pages',
        'config' => [
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => Page::TABLE_NAME,
            'foreign_table' => Page::TABLE_NAME,
            'minitems' => 1,
            'maxitems' => 100
        ],
    ];
}

return $formsTca;
