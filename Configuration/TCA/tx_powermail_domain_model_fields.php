<?php
$fieldsTca = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields',
		'label' => 'title',
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
		'sortby' => 'sorting',
		'default_sortby' => 'ORDER BY sorting',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'requestUpdate' => 'type,validation,own_marker_select',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('powermail') .
			'Resources/Public/Icons/tx_powermail_domain_model_fields.gif'
	),
	'interface' => array(
		'showRecordFieldList' =>
			'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, type, settings,
			path, content_element, text, prefill_value, placeholder, create_from_typoscript, mandatory,
			validation, validation_configuration, css, description, multiselect, datepicker_settings,
			feuser_value, sender_email, sender_name, own_marker_select, auto_marker, marker',
	),
	'types' => array(
		'1' => array(
			'showitem' =>
				'pages, title, type, settings, path, content_element, text,
				--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.palette1;1,
				--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.sheet1,
				--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation_title;2,
				--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.prefill_title;3,
				--palette--;Layout;4,
				description,
				--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.marker_title;5,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access, sys_language_uid;;;;1-1-1,
				l10n_parent, l10n_diffsource, hidden;;1,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => 'sender_email, sender_name', '', 'canNotCollapse' => 1),
		'2' => array('showitem' => 'mandatory, validation, validation_configuration', '', 'canNotCollapse' => 1),
		'3' => array('showitem' => 'prefill_value, placeholder, feuser_value, create_from_typoscript', '', 'canNotCollapse' => 1),
		'4' => array('showitem' => 'css, multiselect, datepicker_settings'),
		'5' => array('showitem' => 'auto_marker, marker, own_marker_select', '', 'canNotCollapse' => 1),
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
				'foreign_table_where' =>
					'AND tx_powermail_domain_model_fields.pid=###CURRENT_PID### AND tx_powermail_domain_model_fields.sys_language_uid IN (-1,0)',
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
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'type' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.spacer1',
						'--div--'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.0',
						'input'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.1',
						'textarea'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.2',
						'select'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.3',
						'check'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.4',
						'radio'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.5',
						'submit'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.spacer2',
						'--div--'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.6',
						'captcha'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.7',
						'reset'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.8',
						'text'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.9',
						'content'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.10',
						'html'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.11',
						'password'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.12',
						'file'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.13',
						'hidden'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.14',
						'date'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.17',
						'country'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.15',
						'location'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.16',
						'typoscript'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.spacer3',
						'--div--'
					),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => 'required',
				'itemsProcFunc' => 'In2code\Powermail\Utility\Tca\AddOptionsToSelection->addOptionsForType',
			),
			'displayCond' => 'FIELD:sys_language_uid:=:0',
		),
		'settings' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.settings',
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
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.path',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
			'displayCond' => 'FIELD:type:IN:typoscript'
		),
		'content_element' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.content_element',
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
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.text',
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
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.sender_email',
			'config' => array(
				'type' => 'check'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,select,check,radio,hidden'
		),
		'sender_name' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.sender_name',
			'config' => array(
				'type' => 'check'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,select,check,radio,hidden'
		),
		'mandatory' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.mandatory',
			'config' => array(
				'type' => 'check'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,select,check,radio,date,password'
		),
		'validation' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:pleaseChoose',
						''
					),

					/**
					 * Spacer
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.spacer1',
						'--div--'
					),

					/**
					 * EMAIL (+html5)
					 *
					 * html5 example: <input type="email" />
					 * javascript example: <input type="text" data-parsley-type="email" />
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation.1',
						1
					),

					/**
					 * URL (+html5)
					 *
					 * html5 example: <input type="url" />
					 * javascript example: <input type="text" data-parsley-type="url" />
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation.2',
						2
					),

					/**
					 * PHONE (+html5)
					 *
					 * html5 example:
					 * 		<input type="text" pattern="[\+]\d{2}[\(]\d{2}[\)]\d{4}[\-]\d{4}" />
					 * javascript example:
					 * 		<input ... data-parsley-pattern="[\+]\d{2}[\(]\d{2}[\)]\d{4}[\-]\d{4}" />
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation.3',
						3
					),

					/**
					 * NUMBER/INTEGER (+html5)
					 *
					 * html5 example: <input type="number" />
					 * javascript example: <input type="text" data-parsley-type="integer" />
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation.4',
						4
					),

					/**
					 * LETTERS (+html5)
					 *
					 * html5 example: <input type="text" pattern="[a-zA-Z]*" />
					 * javascript example: <input type="text" data-parsley-pattern="[a-zA-Z]*" />
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation.5',
						5
					),

					/**
					 * Spacer
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.spacer2',
						'--div--'
					),

					/**
					 * MIN NUMBER (+html5)
					 *
					 * Note: Field validation_configuration for editors viewable
					 * html5 example: <input type="text" min="6" />
					 * javascript example: <input type="text" data-parsley-min="6" />
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation.6',
						6
					),

					/**
					 * MAX NUMBER (+html5)
					 *
					 * Note: Field validation_configuration for editors viewable
					 * html5 example: <input type="text" max="12" />
					 * javascript example: <input type="text" data-parsley-max="12" />
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation.7',
						7
					),

					/**
					 * RANGE (+html5)
					 *
					 * Note: Field validation_configuration for editors viewable
					 * html5 example: <input type="range" min="1" max="10" />
					 * javascript example:
					 * 		<input type="text" data-parsley-type="range" min="1" max="10" />
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation.8',
						8
					),

					/**
					 * LENGTH
					 *
					 * Note: Field validation_configuration for editors viewable
					 * javascript example:
					 * 		<input type="text" data-parsley-length="[6, 10]" />
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation.9',
						9
					),

					/**
					 * PATTERN (+html5)
					 *
					 * Note: Field validation_configuration for editors viewable
					 * html5 example: <input type="text" pattern="https?://.+" />
					 * javascript example:
					 * 		<input type="text" data-parsley-pattern="https?://.+" />
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validation.10',
						10
					),

					/**
					 * Spacer
					 */
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.type.spacer3',
						'--div--'
					),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => '',
				'itemsProcFunc' => '\In2code\Powermail\Utility\Tca\AddOptionsToSelection->addOptionsForValidation',
			),
			'displayCond' => 'FIELD:type:IN:input,textarea'
		),
		'validation_configuration' => array(
			'exclude' => 1,
			'label' =>
				'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.validationConfiguration',
			'config' => array(
				'type' => 'input',
				'size' => 30
			),
			'displayCond' => 'FIELD:validation:>:5'
		),
		'prefill_value' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.prefill_value',
			'config' => array(
				'type' => 'text',
				'cols' => '32',
				'rows' => '2'
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,hidden,country'
		),
		'placeholder' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.placeholder',
			'config' => array(
				'type' => 'input',
				'size' => 10
			),
			'displayCond' => 'FIELD:type:IN:input'
		),
		'feuser_value' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.feuser_value',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:pleaseChoose',
						''
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.feuser_value.name',
						'name'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.feuser_value.address',
						'address'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.feuser_value.phone',
						'telephone'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.feuser_value.fax',
						'fax'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.feuser_value.email',
						'email'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.feuser_value.zip',
						'zip'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.feuser_value.city',
						'city'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.feuser_value.country',
						'country'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.feuser_value.www',
						'www'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.feuser_value.company',
						'company'
					),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => '',
				'itemsProcFunc' => 'In2code\Powermail\Utility\Tca\AddOptionsToSelection->addOptionsForFeUserProperty',
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,select,check,radio,hidden,country'
		),
		'create_from_typoscript' => array(
			'exclude' => 1,
			'label' =>
				'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.create_from_typoscript',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
			'displayCond' => 'FIELD:type:IN:select,radio,check'
		),
		'css' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.css',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:pleaseChoose',
						''
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.css.1',
						'layout1'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.css.2',
						'layout2'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.css.3',
						'layout3'
					),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			),
			'displayCond' => 'FIELD:type:IN:input,textarea,select,check,radio,submit,password,file,location,text,date,country'
		),
		'multiselect' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.multiselect',
			'config' => array(
				'type' => 'check'
			),
			'displayCond' => 'FIELD:type:IN:select,file'
		),
		'datepicker_settings' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' =>
				'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.datepicker_settings',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.datepicker_settings.1',
						'date'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.datepicker_settings.2',
						'datetime'
					),
					array(
						'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.datepicker_settings.3',
						'time'
					),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			),
			'displayCond' => 'FIELD:type:IN:date'
		),
		'auto_marker' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.auto_marker',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'In2code\Powermail\Utility\Tca\Marker->createMarker'
			),
			'displayCond' => 'FIELD:own_marker_select:REQ:false'
		),
		'marker' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.own_marker',
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
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.own_marker_select',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'description' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.description',
			'config' => array(
				'type' => 'text',
				'cols' => '32',
				'rows' => '2',
				'eval' => 'trim'
			),
			'displayCond' => 'FIELD:type:!IN:content,hidden,html,reset,submit,text,typoscript'
		),
		'pages' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_fields.pages',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_powermail_domain_model_pages',
				'foreign_table_where' =>
					'AND tx_powermail_domain_model_pages.pid=###CURRENT_PID###
					AND tx_powermail_domain_model_pages.sys_language_uid IN (-1,###REC_FIELD_sys_language_uid###)',
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