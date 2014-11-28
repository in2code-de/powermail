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
$uncachedFormActions = 'form';
if ($confArr['enableCaching'] == 1) {
	$uncachedFormActions = '';
}
$uncachedFormActions .= ', create, confirmation, optinConfirm, validateAjax, marketing';

/**
 * Include Frontend Plugins for Powermail
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'In2code.' . $_EXTKEY,
	'Pi1',
	array(
		'Form' => 'form, create, confirmation, optinConfirm, validateAjax, marketing'
	),
	array(
		'Form' => $uncachedFormActions
	)
);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'In2code.' . $_EXTKEY,
	'Pi2',
	array(
		'Output' => 'list, show, edit, update, export, rss, delete'
	),
	array(
		'Output' => 'list, edit, update, export, rss, delete'
	)
);

/**
 * Show Forms in Page Module
 */
/*
$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_powermail_domain_model_forms'][0] = array(
	'fList' => 'uid,title',
	'icon' => TRUE,
);
*/

/**
 * Hook to show PluginInfo
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$_EXTKEY . '_pi1'][$_EXTKEY] =
	'EXT:' . $_EXTKEY . '/Classes/Utility/Hook/PluginInfo.php:In2code\Powermail\Utility\Hook\PluginInfo->getInfo';

/**
 * Hook for first fill of marker field in backend
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
	'EXT:' . $_EXTKEY . '/Classes/Utility/Hook/InitialMarker.php:In2code\Powermail\Utility\Hook\InitialMarker';

/**
 * JavaScript evaluation of TCA fields
 */
$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['\In2code\Powermail\Utility\Tca\EvaluateEmail'] =
	'EXT:powermail/Classes/Utility/Tca/EvaluateEmail.php';

/**
 * eID to get location from geo coordinates
 */
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['powermailEidGetLocation'] =
	'EXT:powermail/Classes/Utility/Eid/GetLocationEid.php';

/**
 * eID to validate form fields
 */
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['powermailEidValidator'] =
	'EXT:powermail/Classes/Utility/Eid/ValidatorEid.php';

/**
 * eID to store marketing information
 */
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['powermailEidMarketing'] =
	'EXT:powermail/Classes/Utility/Eid/MarketingEid.php';

/**
 * CommandController for powermail tasks
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
	'In2code\\Powermail\\Command\\TaskCommandController';