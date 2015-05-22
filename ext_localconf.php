<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

/**
 * Enable caching for show action in form controller
 */
$uncachedFormActions = 'form';
if (\In2code\Powermail\Utility\Configuration::isEnableCachingActive()) {
	$uncachedFormActions = '';
}
$uncachedFormActions .= ', create, confirmation, optinConfirm, marketing';

/**
 * Include Frontend Plugins for Powermail
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'In2code.' . $_EXTKEY,
	'Pi1',
	array(
		'Form' => 'form, create, confirmation, optinConfirm, marketing'
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
 * Hook to show PluginInfo
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$_EXTKEY . '_pi1'][$_EXTKEY] =
	'EXT:' . $_EXTKEY . '/Classes/Utility/Hook/PluginInformation.php:In2code\Powermail\Utility\Hook\PluginInformation->build';

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
 * eID to store marketing information
 */
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['powermailEidMarketing'] =
	'EXT:powermail/Classes/Utility/Eid/MarketingEid.php';

/**
 * CommandController for powermail tasks
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
	'In2code\\Powermail\\Command\\TaskCommandController';