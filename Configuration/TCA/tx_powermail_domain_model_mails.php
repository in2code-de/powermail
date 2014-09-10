<?php
$mailsTca = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails',
		'label' => 'sender_mail',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'default_sortby' => 'ORDER BY crdate DESC',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('powermail') .
			'Resources/Public/Icons/tx_powermail_domain_model_mails.gif',
		'searchFields' => 'sender_mail, sender_name, subject, body'
	),
	'interface' => array(
		'showRecordFieldList' =>
			'sys_language_uid, l10n_parent, l10n_diffsource, hidden, crdate, receiver_mail,
			sender_name, sender_mail,
			subject, form, answers, body, feuser, spam_factor,
			time, sender_ip, user_agent, marketing_referer_domain, marketing_referer,
			marketing_country, marketing_mobile_device, marketing_frontend_language, marketing_browser_language, marketing_page_funnel',
	),
	'types' => array(
		'1' => array(
			'showitem' =>
				'crdate, receiver_mail,
				--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.palette1;1,
				subject, body;;;richtext[],
				--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.sheet1,
				form, answers, feuser, spam_factor, time, sender_ip, user_agent,
				--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.sheet2,
				marketing_referer_domain, marketing_referer, marketing_country, marketing_mobile_device,
				marketing_frontend_language, marketing_browser_language, marketing_page_funnel,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access, hidden;;1, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => 'sender_name, sender_mail'),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_powermail_domain_model_mails',
				'foreign_table_where' =>
					'AND tx_powermail_domain_model_mails.pid=###CURRENT_PID### AND tx_powermail_domain_model_mails.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'crdate' => array(
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.crdate',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'datetime',
				'readOnly' => 1
			),
		),
		'receiver_mail' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.receiver_mail',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5'
			),
		),
		'sender_mail' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.sender_mail',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'sender_name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.sender_name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'subject' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.subject',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'body' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.body',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array (
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'RTE',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			),
		),
		'form' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.form',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_powermail_domain_model_forms',
				'foreign_table_where' =>
					'AND tx_powermail_domain_model_forms.deleted = 0 AND tx_powermail_domain_model_forms.hidden = 0
					order by tx_powermail_domain_model_forms.title',
			),
		),
		'answers' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.answers',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_powermail_domain_model_answers',
				'foreign_field' => 'mail',
				'maxitems'      => 1000,
				'appearance' => array(
					'collapse' => 1,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				),
			),
		),
		'feuser' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.feuser',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1
			)
		),
		'spam_factor' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.spam_factor',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'eval' => 'trim',
				'readOnly' => 1
			),
		),
		'time' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.time',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'timesec',
				'checkbox' => 0,
				'default' => 0,
				'readOnly' => 1
			),
		),
		'sender_ip' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.sender_ip',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim',
				'readOnly' => 1
			),
		),
		'user_agent' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.user_agent',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim',
				'readOnly' => 1
			),
		),
		'marketing_referer_domain' => array(
			'exclude' => 1,
			'label' =>
				'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.marketing_referer_domain',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1
			),
		),
		'marketing_referer' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.marketing_referer',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'readOnly' => 1
			),
		),
		'marketing_country' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.marketing_country',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1
			),
		),
		'marketing_mobile_device' => array(
			'exclude' => 1,
			'label' =>
				'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.marketing_mobile_device',
			'config' => array(
				'type' => 'check',
				'readOnly' => 1
			),
		),
		'marketing_frontend_language' => array(
			'exclude' => 1,
			'label' =>
				'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.marketing_frontend_language',
			'config' => array(
				'type' => 'input',
				'size' => 2,
				'eval' => 'int',
				'readOnly' => 1
			),
		),
		'marketing_browser_language' => array(
			'exclude' => 1,
			'label' =>
				'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.marketing_browser_language',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1
			),
		),
		'marketing_page_funnel' => array(
			'exclude' => 1,
			'label' =>
				'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_mails.marketing_page_funnel',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'readOnly' => 1
			),
		),
	),
);

return $mailsTca;