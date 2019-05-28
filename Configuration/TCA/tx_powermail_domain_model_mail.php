<?php
use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\ConfigurationUtility;

$typeDefault = 'crdate, receiver_mail, ' .
    '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Mail::TABLE_NAME . '.palette1;1, ' .
    'subject, body, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet1, ' .
    'form, answers, feuser, spam_factor, time, sender_ip, user_agent, ' .
    '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Field::TABLE_NAME . '.sheet2, ' .
    'marketing_referer_domain, marketing_referer, marketing_country, marketing_mobile_device, ' .
    'marketing_frontend_language, marketing_browser_language, marketing_page_funnel, ' .
    '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden, starttime, endtime';

$mailsTca = [
    'ctrl' => [
        'title' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Mail::TABLE_NAME,
        'label' => 'sender_mail',
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
        'iconfile' => ConfigurationUtility::getIconPath(Mail::TABLE_NAME . '.gif'),
        'searchFields' => 'sender_mail, sender_name, subject, body'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, ' .
            'crdate, receiver_mail, sender_name, sender_mail, subject, form, answers, body, ' .
            'feuser, spam_factor, time, sender_ip, user_agent, marketing_referer_domain, ' .
            'marketing_referer, marketing_country, marketing_mobile_device, ' .
            'marketing_frontend_language, marketing_browser_language, marketing_page_funnel',
    ],
    'types' => [
        '1' => [
            'showitem' => $typeDefault
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => 'sender_name, sender_mail'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'renderType' => 'selectSingle',
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
                'foreign_table' => Mail::TABLE_NAME,
                'foreign_table_where' => 'and ' . Mail::TABLE_NAME . '.pid=###CURRENT_PID### and ' .
                    Mail::TABLE_NAME . '.sys_language_uid IN (-1,0)',
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
        'crdate' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Mail::TABLE_NAME . '.crdate',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'size' => 30,
                'readOnly' => 1
            ],
        ],
        'receiver_mail' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.receiver_mail',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            ],
        ],
        'sender_mail' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.sender_mail',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'sender_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.sender_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'subject' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Mail::TABLE_NAME . '.subject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'body' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Mail::TABLE_NAME . '.body',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'enableRichtext' => true,
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => '',
                        'options' => [
                            'title' => 'RTE'
                        ]
                    ]
                ],
                'wizards' => [
                    '_PADDING' => 2,
                    'RTE' => [
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'RTE',
                        'icon' => 'actions-wizard-rte',
                        'module' => [
                            'name' => 'wizard_rte'
                        ]
                    ],
                ],
            ],
        ],
        'form' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Mail::TABLE_NAME . '.form',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => Form::TABLE_NAME,
                'foreign_table_where' => 'and ' . Form::TABLE_NAME . '.deleted = 0 and ' .
                    Form::TABLE_NAME . '.hidden = 0 order by ' . Form::TABLE_NAME . '.title',
            ],
        ],
        'answers' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Mail::TABLE_NAME . '.answers',
            'config' => [
                'type' => 'inline',
                'foreign_table' => Answer::TABLE_NAME,
                'foreign_field' => 'mail',
                'maxitems' => 1000,
                'appearance' => [
                    'collapse' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],
        'feuser' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Mail::TABLE_NAME . '.feuser',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'fe_users',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 0
            ]
        ],
        'spam_factor' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.spam_factor',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'eval' => 'trim',
                'readOnly' => 1
            ],
        ],
        'time' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' . Mail::TABLE_NAME . '.time',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'timesec',
                'size' => 13,
                'checkbox' => 0,
                'default' => 0,
                'readOnly' => 1
            ],
        ],
        'sender_ip' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.sender_ip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => 1,
                'default' => ''
            ],
        ],
        'user_agent' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.user_agent',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => 1,
                'default' => ''
            ],
        ],
        'marketing_referer_domain' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.marketing_referer_domain',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => 1,
                'default' => ''
            ],
        ],
        'marketing_referer' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.marketing_referer',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'readOnly' => 1,
                'default' => ''
            ],
        ],
        'marketing_country' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.marketing_country',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => 1,
                'default' => ''
            ],
        ],
        'marketing_mobile_device' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.marketing_mobile_device',
            'config' => [
                'type' => 'check',
                'readOnly' => 1,
                'default' => 0
            ],
        ],
        'marketing_frontend_language' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.marketing_frontend_language',
            'config' => [
                'type' => 'input',
                'size' => 2,
                'eval' => 'int',
                'readOnly' => 1,
                'default' => 0
            ],
        ],
        'marketing_browser_language' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.marketing_browser_language',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => 1,
                'default' => ''
            ],
        ],
        'marketing_page_funnel' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:' .
                Mail::TABLE_NAME . '.marketing_page_funnel',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'readOnly' => 1,
                'default' => ''
            ],
        ],
        'uid' => [
            'exclude' => 1,
            'label' => 'UID',
            'config' => [
                'type' => 'none',
            ],
        ],
    ],
];

if (ConfigurationUtility::isDisableMarketingInformationActive()) {
    foreach (array_keys($mailsTca['columns']) as $columnName) {
        if (strpos($columnName, 'marketing_') === 0) {
            unset($mailsTca['columns'][$columnName]);
        }
    }
}

return $mailsTca;
