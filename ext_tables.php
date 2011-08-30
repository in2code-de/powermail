<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$extPath = t3lib_extMgm::extPath($_EXTKEY);
$extRelPath = t3lib_extMgm::extRelPath($_EXTKEY);
$extIconPath = $extRelPath . 'res/img/';

t3lib_extMgm::addStaticFile($_EXTKEY, 'static/pi1/', 'Powermail');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/css_basic/', 'Powermail basic CSS');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/css_fancy/', 'Powermail fancy CSS');
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
t3lib_extMgm::allowTableOnStandardPages('tx_powermail_fieldsets');


require_once($extPath . 'lib/class.user_powermail_tx_powermail_forms_recip_id.php');
if (TYPO3_MODE == 'BE' || (TYPO3_MODE == 'FE' && t3lib_div::int_from_ver(TYPO3_version) > t3lib_div::int_from_ver('4.2.99') && isset($GLOBALS['BE_USER']) && $GLOBALS['BE_USER']->isFrontendEditingActive())) {
	require_once($extPath . 'lib/class.user_powermail_tx_powermail_forms_recip_table.php');
	require_once($extPath . 'lib/class.user_powermail_tx_powermail_forms_preview.php');
	require_once($extPath . 'lib/class.user_powermail_tx_powermail_forms_sender_field.php');
	require_once($extPath . 'lib/class.user_powermail_tx_powermail_fields_fe_field.php');
	require_once($extPath . 'lib/class.user_powermail_tx_powermail_example.php');
	require_once($extPath . 'lib/class.user_powermail_tx_powermail_uid.php');
	require_once($extPath . 'lib/user_powermail_updateError.php');
}

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi1'] = 'layout,select_key,pages,recursive';

t3lib_extMgm::addToInsertRecords('tx_powermail_fieldsets');

$GLOBALS['TCA']['tx_powermail_fieldsets'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_fieldsets',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'sortby' => 'sorting',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime'
		),
		'dynamicConfigFile' => $extPath . 'tca.php',
		'iconfile' => $extIconPath . 'icon_tx_powermail_fieldsets.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'fe_group, form, title, felder, hidden',
	)
);


t3lib_extMgm::allowTableOnStandardPages('tx_powermail_fields');

t3lib_extMgm::addToInsertRecords('tx_powermail_fields');

$GLOBALS['TCA']['tx_powermail_fields'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_fields',
		'requestUpdate' => 'formtype',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime'
		),
		'dynamicConfigFile' => $extPath . 'tca.php',
		'iconfile' => $extIconPath . 'icon_tx_powermail_fields.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'fieldset, title, name, flexform, value, size, maxsize, max, min, step, pattern, placeholder, mandantory, more, fe_field, hidden',
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_powermail_mails');

t3lib_extMgm::addToInsertRecords('tx_powermail_mails');

$GLOBALS['TCA']['tx_powermail_mails'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails',
		'label' => 'sender',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate DESC',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => $extPath . 'tca.php',
		'iconfile' => $extIconPath . 'icon_tx_powermail_mails.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'formid, recipient, subject_r, sender, content, piVars, senderIP, UserAgent, Referer, SP_TZ, hidden',
	)
);

t3lib_div::loadTCA('tt_content');

t3lib_extMgm::addPlugin(
	array(
		 'LLL:EXT:powermail/locallang_db.xml:tt_content.CType_pi1',
		 $_EXTKEY . '_pi1',
		 $extRelPath . 'ext_icon.gif'
	),
	'CType'
);

$tempColumns = Array(
	'tx_powermail_title' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.title',
		'config' => Array(
			'type' => 'input',
			'size' => '30',
			'max' => '30',
			'eval' => 'required,trim,lower,alphanum_x,nospace',
		)
	),
	'tx_powermail_recipient' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recipient',
		'config' => Array(
			'type' => 'text',
			'cols' => '60',
			'rows' => '2',
		)
	),
	'tx_powermail_subject_r' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.subject_r',
		'config' => Array(
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_powermail_subject_s' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.subject_s',
		'config' => Array(
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_powermail_sender' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.sender',
		'config' => Array(
			'type' => 'select',
			'items' => Array(
				Array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_table.I.0', '0'),
			),
			'itemsProcFunc' => 'user_powermail_tx_powermail_forms_sender_field->main',
			'size' => 1,
			'maxitems' => 1,
		)
	),
	'tx_powermail_sendername' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.sendername',
		'config' => Array(
			'type' => 'select',
			'items' => array(),
			'itemsProcFunc' => 'user_powermail_tx_powermail_forms_sender_field->main',
			'size' => 3,
			'maxitems' => 10,
		)
	),
	'tx_powermail_confirm' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.confirm',
		'config' => Array(
			'type' => 'check',
			'default' => 1,
		)
	),
	'tx_powermail_pages' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.savemailstopage',
		'config' => Array(
			'type' => 'group',
			'internal_type' => 'db',
			'allowed' => 'pages',
			'size' => '1',
			'maxitems' => '1',
			'minitems' => '0',
			'show_thumbs' => '1',
			'wizards' => array(
				'suggest' => array(
					'type' => 'suggest'
				)
			)
		)
	),
	'tx_powermail_multiple' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.multiple',
		'config' => Array(
			'type' => 'select',
			'items' => array(
				Array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.multiple.I.0', '0'),
				Array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.multiple.I.1', '1'),
				Array('LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.multiple.I.2', '2'),
			),
		)
	),
	'tx_powermail_recip_table' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_table',
		'config' => Array(
			'type' => 'select',
			'items' => Array(
				Array('', '0'),
			),
			'itemsProcFunc' => 'user_powermail_tx_powermail_forms_recip_table->main',
			'size' => 1,
			'maxitems' => 1,
		)
	),
	'tx_powermail_recip_id' => Array(
		'exclude' => 1,
		'displayCond' => 'FIELD:tx_powermail_recip_table:REQ:true',
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.recip_id',
		'config' => Array(
			'type' => 'select',
			'items' => Array(
			),
			'itemsProcFunc' => 'user_powermail_tx_powermail_forms_recip_id->main',
			'size' => 5,
			'maxitems' => 100,
			'allowNonIdValues' => 1,
		)
	),
	'tx_powermail_thanks' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.thanks',
		'config' => Array(
			'type' => 'text',
			'cols' => '60',
			'rows' => '2',
			'default' => '###POWERMAIL_ALL###',
		)
	),
	'tx_powermail_mailsender' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.mailsender',
		'config' => Array(
			'type' => 'text',
			'cols' => '60',
			'rows' => '2',
			'default' => '###POWERMAIL_ALL###',
		)
	),
	'tx_powermail_mailreceiver' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.mailreceiver',
		'config' => Array(
			'type' => 'text',
			'cols' => '60',
			'rows' => '2',
			'default' => '###POWERMAIL_ALL###',
		)
	),
	'tx_powermail_redirect' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.redirect',
		'config' => Array(
			'type' => 'input',
			'size' => '30',
			'wizards' => Array(
				'_PADDING' => 2,
				'link' => Array(
					'type' => 'popup',
					'title' => 'Link',
					'icon' => 'link_popup.gif',
					'script' => 'browse_links.php?mode=wizard',
					'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
				),
			),
		)
	),
	'user_powermail_updateError' => Array(
		'label' => 'Powermail error',
		'config' => Array(
			'type' => 'user',
			'userFunc' => 'user_powermail_updateError->user_updateError'
		)
	),
	'tx_powermail_fieldsets' => Array(
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.fieldsets',
		'config' => Array(
			'type' => 'inline',
			'foreign_table' => 'tx_powermail_fieldsets',
			'foreign_field' => 'tt_content',
			'foreign_sortby' => 'sorting',
			'foreign_label' => 'title',
			'maxitems' => 1000,
			'appearance' => Array(
				'collapseAll' => 1,
				'expandSingle' => 1,
				'useSortable' => 1,
				'newRecordLinkAddTitle' => 1,
				'levelLinksPosition' => 'both',
				'showSynchronizationLink' => 0,
				'showAllLocalizationLink' => 1,
				'showPossibleLocalizationRecords' => 1,
				'showRemovedLocalizationRecords' => 1,
			),
			'behaviour' => array(
				'localizeChildrenAtParentLocalization' => 1,
				'localizationMode' => 'select',
			),
		)
	),
	'tx_powermail_users' => Array(
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.users',
		'config' => Array(
			'type' => 'passthrough'
		)
	),
	'tx_powermail_preview' => Array(
		'label' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.preview',
		'config' => Array(
			'type' => 'user',
			'userFunc' => 'user_powermail_tx_powermail_forms_preview->main',
		)
	),
);

// If db-storing is disabled, clear tx_powermail_pages
if ($confArr['disableBackendModule'] == 1) {
	unset($tempColumns['tx_powermail_pages']);
}

// If preview window is deactivated, clear tx_powermail_preview
if ($confArr['usePreview'] != 1) {
	unset($tempColumns['tx_powermail_preview']);
}

// If settings for powermail found in localconf, clear user_powermail_updateError
if (strlen($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']) > 1) {
	unset($tempColumns['user_powermail_updateError']);
}

//t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content', $tempColumns, 1);

$GLOBALS['TCA']['tt_content']['types'][$_EXTKEY . '_pi1']['showitem'] = '
	CType;;4;button;1-1-1, hidden,1-1-1, header;;3;;3-3-3, linkToTop;;;;3-3-3,
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div1, tx_powermail_title;;;;2-2-2, ' . ($confArr['disableBackendModule'] != 1 ? 'tx_powermail_pages;;;;1-1-1, ' : '') . 'tx_powermail_confirm;;;;3-3-3, tx_powermail_multiple,
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div2, tx_powermail_fieldsets;;;;4-4-4, user_powermail_updateError, ' . ($confArr['usePreview'] != 1 ? 'tx_powermail_preview' : '') . ',
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div3, tx_powermail_sender, tx_powermail_sendername, tx_powermail_subject_s,, tx_powermail_mailsender;;;richtext:rte_transform[mode=ts],
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div4, tx_powermail_subject_r, tx_powermail_recipient, tx_powermail_users;;;;5-5-5,tx_powermail_recip_table, tx_powermail_recip_id, tx_powermail_mailreceiver;;;richtext:rte_transform[mode=ts],
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div5, tx_powermail_thanks;;;richtext:rte_transform[mode=ts], tx_powermail_redirect,
	--div--;LLL:EXT:powermail/locallang_db.xml:tx_powermail_forms.div8, starttime, endtime, fe_group';

// Add "tx_powermail_recip_table" to the requestUpdate
$GLOBALS['TCA']['tt_content']['ctrl']['requestUpdate'] .= $GLOBALS['TCA']['tt_content']['ctrl']['requestUpdate'] ? ',tx_powermail_recip_table' : 'tx_powermail_recip_table';

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_powermail_pi1_wizicon'] = $extPath . 'pi1/class.tx_powermail_pi1_wizicon.php';
	if ($confArr['disableBackendModule'] !== '1') {
		t3lib_extMgm::addModule('web', 'txpowermailM1', '', $extPath . 'mod1/');
	}
}
?>