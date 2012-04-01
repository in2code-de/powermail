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
$uncachedFormActions = 'form, create, confirmation';
if ($confArr['enableCaching'] == 1) {
	$uncachedFormActions = 'create, confirmation';
}

/**
 * Include Frontend Plugins for Powermail
 */
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'Forms' => 'form, create, confirmation'
	),
	array(
		'Forms' => $uncachedFormActions
	)
);
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi2',
	array(
		'Output' => 'list, show'
	),
	array(
		'Output' => 'list'
	)
);

/**
 * Hooking for PluginInfo
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$_EXTKEY . '_pi1'][$_EXTKEY] =
	'EXT:' . $_EXTKEY . '/Classes/Utility/PluginInfo.php:Tx_Powermail_Utility_PluginInfo->getInfo';

/**
 * Extra evaluation of TCA fields
 */
$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['Tx_Powermail_Utility_EvaluateEmail'] = 'EXT:powermail/Classes/Utility/EvaluateEmail.php';

?>