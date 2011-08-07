<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	include_once(t3lib_extMgm::extPath('powermail') . 'lib/class.user_powermail_tx_powermail_fieldsetchoose.php');
}

$TYPO3_CONF_VARS['BE']['AJAX']['tx_powermail::controller'] = t3lib_extMgm::extPath('powermail') . 'mod1/class.tx_powermail_ajax.php:tx_powermail_Ajax->ajaxController';

$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']); // Get backend config
include_once(t3lib_extMgm::extPath('powermail') . 'lib/user_powermailOnCurrentPage.php'); // Conditions for JS including
include_once(t3lib_extMgm::extPath('powermail') . 'lib/user_powermail_misc.php'); // Some powermail userFunc (Conditions if any further step)
include_once(t3lib_extMgm::extPath('powermail') . 'lib/user_powermailCheckT3jquery.php'); // Conditions for Check if t3jquery is loaded or not
include_once(t3lib_extMgm::extPath('powermail') . 'lib/user_powermailCheckT3jqueryCDNMode.php'); // Conditions for Check if t3jquery is in CDN Mode
include_once(t3lib_extMgm::extPath('powermail') . 'cli/class.tx_powermail_scheduler_addFields.php'); // Scheduler addFields class

t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:powermail/pageTSconfig.txt">');

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_powermail_pi1.php', '_pi1', 'CType', 1);

$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_powermail_fieldsets'][0] = array(
	'fList' => 'uid,title',
	'icon' => TRUE,
);
$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_powermail_fields'][0] = array(
	'fList' => 'uid,title,name,type,fieldset',
	'icon' => TRUE,
);

/* SCHEDULER SETTINGS */
$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['tx_powermail_scheduler'] = array(
	'extension' => 'powermail',
	'title' => 'Automatic Export Mails',
	'description' => 'Send your CSV, XLS or HTML exports via Email to a defined target',
	'additionalFields' => 'tx_powermail_scheduler_addFields'
);

?>