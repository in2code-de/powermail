<?php
use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\ConfigurationUtility;

$answersTca = [
    'ctrl' => [
        'title' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Answer::TABLE_NAME,
        'label' => 'value',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY crdate DESC',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'iconfile' => ConfigurationUtility::getIconPath(Answer::TABLE_NAME . '.gif')
    ],
    'interface' => [
        'showRecordFieldList' =>
            'sys_language_uid, l10n_parent, l10n_diffsource, hidden, value, value_type, field, mail',
    ],
    'types' => [
        '1' => ['showitem' => 'value, value_type, field, mail'],
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
                ]
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
                'foreign_table' => Answer::TABLE_NAME,
                'foreign_table_where' => 'AND ' . Answer::TABLE_NAME . '.pid=###CURRENT_PID### AND ' .
                    Answer::TABLE_NAME . '.sys_language_uid IN (-1,0)',
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
            'exclude' => 1,
            'l10n_mode' => 'exclude',
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
            'exclude' => 1,
            'l10n_mode' => 'exclude',
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
        'value' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Answer::TABLE_NAME . '.value',
            'config' => [
                'type' => 'text',
                'cols' => '60',
                'rows' => '3'
            ],
        ],
        'value_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Answer::TABLE_NAME . '.value_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    /**
                     * Text
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Answer::TABLE_NAME . '.value_type.0',
                        '0'
                    ],
                    /**
                     * Multi Text (Array)
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Answer::TABLE_NAME . '.value_type.1',
                        '1'
                    ],
                    /**
                     * Date
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Answer::TABLE_NAME . '.value_type.2',
                        '2'
                    ],
                    /**
                     * Upload
                     */
                    [
                        'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                        Answer::TABLE_NAME . '.value_type.3',
                        '3'
                    ],
                ],
            ],
        ],
        'field' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Answer::TABLE_NAME . '.field',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => Field::TABLE_NAME,
                'size' => 1,
                'maxitems' => 1,
                'multiple' => 0,
                'default' => 0
            ],
        ],
        'mail' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Answer::TABLE_NAME . '.mail',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => Mail::TABLE_NAME,
                'size' => 1,
                'maxitems' => 1,
                'multiple' => 0,
                'default' => 0
            ],
        ],
    ],
];

return $answersTca;
