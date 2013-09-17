<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_powermail_domain_model_fields'] = array(
	'ctrl' => $TCA['tx_powermail_domain_model_fields']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, type, settings, path, content_element, text, prefill_value, mandatory, validation, css, feuser_value, sender_email, sender_name, own_marker_select, auto_marker, marker',
	),
	'types' => array(
		'1' => array('showitem' => 'title, type, settings, path, content_element, text, --palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.palette1;1, --div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.sheet1, validation_title, --palette--;Validation;2, prefill_title, --palette--;Prefill;3, css, marker_title, --palette--;Variables;4,--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => 'sender_email, sender_name'),
		'2' => array('showitem' => 'mandatory, validation'),
		'3' => array('showitem' => 'prefill_value, feuser_value'),
		'4' => array('showitem' => 'auto_marker, marker, own_marker_select'),
		'canNotCollapse' => '1'
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
				'foreign_table' => 'tx_powermail_domain_model_fields',
				'foreign_table_where' => 'AND tx_powermail_domain_model_fields.pid=###CURRENT_PID### AND tx_powermail_domain_model_fields.sys_language_uid IN (-1,0)',
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
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
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
			'l10n_mode' => 'exclude',
			'exclude' => 1,
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
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'type' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.spacer1',
						'--div--'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.0',
						'input'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.1',
						'textarea'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.2',
						'select'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.3',
						'check'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.4',
						'radio'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.5',
						'submit'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.spacer2',
						'--div--'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.6',
						'captcha'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.7',
						'reset'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.8',
						'text'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.9',
						'content'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.10',
						'html'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.11',
						'password'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.12',
						'file'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.13',
						'hidden'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.14',
						'date'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.15',
						'location'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.16',
						'typoscript'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.type.spacer3',
						'--div--'
					),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => 'required',
				'itemsProcFunc' => 'Tx_Powermail_Utility_FlexFormFieldSelection->addOptions',
				'itemsProcFuncFieldName' => 'type'
			),
			'displayCond' => 'FIELD:sys_language_uid:=:0',
		),
		'settings' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.settings',
			'config' => array(
				'type' => 'text',
				'cols' => '32',
				'rows' => '5'
			),
			'displayCond' => 'FIELD:type:IN:select,check,radio'
		),
		'path' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.path',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
			'displayCond' => 'FIELD:type:IN:typoscript'
		),
		'content_element' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.content_element',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tt_content',
				'size' => 1,
				'maxitems' => 1,
				'minitems' => 0
			),
			'displayCond' => 'FIELD:type:IN:content'
		),
		'text' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.text',
			'config' => array(
				'type' => 'text',
				'cols' => '32',
				'rows' => '5'
			),
			'displayCond' => 'FIELD:type:IN:text,html'
		),
		'sender_email' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.sender_email',
			'config' => array(
				'type' => 'check'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,select,check,radio,hidden'
		),
		'sender_name' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.sender_name',
			'config' => array(
				'type' => 'check'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,select,check,radio,hidden'
		),
		'validation_title' => array(
			'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.validation_title',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'Tx_Powermail_Utility_Marker->doNothing'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,select,check,radio'
		),
		'mandatory' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.mandatory',
			'config' => array(
				'type' => 'check'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,select,check,radio,date,password'
		),
		'validation' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.validation',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:pleaseChoose',
						''
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.validation.1',
						1
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.validation.2',
						2
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.validation.3',
						3
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.validation.4',
						4
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.validation.5',
						5
					),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => '',
				'itemsProcFunc' => 'Tx_Powermail_Utility_FlexFormFieldSelection->addOptions',
				'itemsProcFuncFieldName' => 'validation'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea'
		),
		'prefill_title' => array(
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.prefill_title',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'Tx_Powermail_Utility_Marker->doNothing'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,hidden'
		),
		'prefill_value' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.prefill_value',
			'config' => array(
				'type' => 'text',
				'cols' => '32',
				'rows' => '2'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,hidden'
		),
		'feuser_value' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.feuser_value',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:pleaseChoose',
						''
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.feuser_value.name',
						'name'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.feuser_value.address',
						'address'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.feuser_value.phone',
						'telephone'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.feuser_value.fax',
						'fax'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.feuser_value.email',
						'email'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.feuser_value.zip',
						'zip'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.feuser_value.city',
						'city'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.feuser_value.country',
						'country'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.feuser_value.www',
						'www'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.feuser_value.company',
						'company'
					),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => '',
				'itemsProcFunc' => 'Tx_Powermail_Utility_FlexFormFieldSelection->addOptions',
				'itemsProcFuncFieldName' => 'feUserProperty'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,select,check,radio,hidden'
		),
		'css' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.css',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:pleaseChoose',
						''
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.css.1',
						'layout1'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.css.2',
						'layout2'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.css.3',
						'layout3'
					),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,select,check,radio,submit,password,file,location,text,date'
		),
		'marker_title' => array(
			'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.marker_title',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'Tx_Powermail_Utility_Marker->doNothing'
			),
		),
		'auto_marker' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.auto_marker',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'Tx_Powermail_Utility_Marker->createMarker'
			),
			'displayCond' => 'FIELD:own_marker_select:REQ:false'
		),
		'marker' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.own_marker',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,alphanum_x,lower,nospace'
			),
			'displayCond' => 'FIELD:own_marker_select:REQ:true'
		),
		'own_marker_select' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xml:tx_powermail_domain_model_fields.own_marker_select',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'pages' => array(
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'passthrough',
			),
		),
		'sorting' => array(
			'config' => array(
				'type' => 'none',
			),
		),
	),
);

/**
 * Switch from l10n_mode "exclude" to "mergeIfNotBlank"
 */
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
if ($confArr['l10n_mode_merge']) {
	$TCA['tx_powermail_domain_model_fields']['columns']['path']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['sender_email']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['sender_name']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['validation_title']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['mandatory']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['validation']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['feuser_value']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['css']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['marker_title']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['auto_marker']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['marker']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['own_marker_select']['l10n_mode'] = 'mergeIfNotBlank';
	$TCA['tx_powermail_domain_model_fields']['columns']['pages']['l10n_mode'] = 'mergeIfNotBlank';
}
?>