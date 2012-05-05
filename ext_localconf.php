<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

/**
 * Get configuration from extension manager
 */
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);

/**
 * Enable caching for show action in form controller
 */
$uncachedFormActions = 'form, create, confirmation, optinConfirm';
if ($confArr['enableCaching'] == 1) {
	$uncachedFormActions = 'create, confirmation, optinConfirm';
}

/**
 * Include Frontend Plugins for Powermail
 */
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'Forms' => 'form, create, confirmation, optinConfirm'
	),
	array(
		'Forms' => $uncachedFormActions
	)
);
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi2',
	array(
		'Output' => 'list, show, edit, update, export, rss'
	),
	array(
		'Output' => 'list, edit, update, export, rss'
	)
);

/**
 * Hooking for PluginInfo
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$_EXTKEY . '_pi1'][$_EXTKEY] =
	'EXT:' . $_EXTKEY . '/Classes/Utility/PluginInfo.php:Tx_Powermail_Utility_PluginInfo->getInfo';

/**
 * Hooking for first fill of marker field in backend
 */
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
	'EXT:' . $_EXTKEY . '/Classes/Utility/InitialMarker.php:Tx_Powermail_Utility_InitialMarker';

/**
 * eID to get location from geo coordinates
 */
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['powermailEidGetLocation'] = 'EXT:powermail/Classes/Utility/EidGetLocation.php';

/**
 * Extra evaluation of TCA fields
 */
$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['Tx_Powermail_Utility_EvaluateEmail'] = 'EXT:powermail/Classes/Utility/EvaluateEmail.php';

?>