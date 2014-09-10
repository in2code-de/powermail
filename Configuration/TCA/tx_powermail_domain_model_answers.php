<?php
$answersTca = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_answers',
		'label' => 'value',
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
			'Resources/Public/Icons/tx_powermail_domain_model_answers.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, value, value_type, field, mail',
	),
	'types' => array(
		'1' => array('showitem' => 'value, value_type, field, mail'),
	),
	'palettes' => array(
		'1' => array(),
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
				'foreign_table' => 'tx_powermail_domain_model_answers',
				'foreign_table_where' =>
					'AND tx_powermail_domain_model_answers.pid=###CURRENT_PID### AND
					tx_powermail_domain_model_answers.sys_language_uid IN (-1,0)',
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
		'value' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_answers.value',
			'config' => array(
				'type' => 'text',
				'cols' => '60',
				'rows' => '3'
			),
		),
		'value_type' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_answers.value_type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					/**
					 * Text
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_answers.value_type.0',
						'0'
					),

					/**
					 * Multi Text (Array)
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_answers.value_type.1',
						'1'
					),

					/**
					 * Date
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_answers.value_type.2',
						'2'
					),

					/**
					 * Upload
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_answers.value_type.3',
						'3'
					),
				),
			),
		),
		'field' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_answers.field',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_powermail_domain_model_fields',
				'size' => 1,
				'maxitems' => 1,
				'multiple' => 0
			),
		),
		'mail' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_answers.mail',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_powermail_domain_model_mails',
				'size' => 1,
				'maxitems' => 1,
				'multiple' => 0
			),
		),
	),
);

return $answersTca;