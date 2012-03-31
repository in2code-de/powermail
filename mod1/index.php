<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   68: class tx_powermail_module1 extends t3lib_SCbase
 *  130:     function main()
 *  149:     function jumpToUrl(URL)
 *  155:     function confirmSubmit(form)
 *  211:     function menuConfig()
 *  229:     function printContent()
 *  242:     function moduleContent()
 *  297:     function stringReplace($content)
 *  309:     function init()
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH . 'init.php');
require_once($BACK_PATH . 'template.php');

// Include backend powermail classes
require_once('class.tx_powermail_belist.php');
require_once('class.tx_powermail_export.php');
require_once('class.tx_powermail_bedetails.php');
require_once('class.tx_powermail_action.php');
require_once('class.tx_powermail_charts.php');

$LANG->includeLLFile('EXT:powermail/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF, 1);

/**
 * Module 'Powermail' for the 'powermail' extension.
 *
 * @author	powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class tx_powermail_module1 extends t3lib_SCbase {

	/**
	 * @var mixed
	 */
	var $pageinfo;

	/**
	 * $LANG object
	 *
	 * @var language
	 */
	var $lang = null;

	/**
	 * TYPO3 $BACK_PATH
	 *
	 * @var string
	 */
	var $back_path = '';

	/**
	 * Action object
	 *
	 * @var tx_powermail_action
	 */
	var $action = null;

	/**
	 * Chart object
	 *
	 * @var tx_powermail_charts
	 */
	var $charts = null;

	/**
	 * BeList object
	 *
	 * @var tx_powermail_belist
	 */
	var $belist = null;

	/**
	 * BeDetails object
	 *
	 * @var tx_powermail_bedetails
	 */
	var $bedetails = null;

	/**
	 * Export object
	 *
	 * @var tx_powermail_export
	 */
	var $export = null;

	/**
	 * Main method of be module
	 * Generates header and module menu
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id)) {

				// Draw the header.
			$this->doc = t3lib_div::makeInstance('bigDoc');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form = '<form action="" method="GET">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
				<script type="text/javascript">
					<!--
					function confirmSubmit(form) {
						if (confirm("' . $LANG->getLL('del_sure') . '")) {
							return true;
						} else {
							return false;
						}
					}
					// -->
				</script>
			';
			$this->doc->postCode = '
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			$headerSection = $this->doc->getHeader('pages', $this->pageinfo, $this->pageinfo['_thePath']) . '<br />';
			$headerSection .= $LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path') . ': ';
			$headerSection .= t3lib_div::fixed_lgd_cs($this->pageinfo['_thePath'], -50);

			$this->content .= $this->doc->startPage($LANG->getLL('title'));
			$this->content .= $this->doc->header($LANG->getLL('title'));
			$this->content .= $this->doc->spacer(5);
			$this->content .= $this->doc->section('', $this->doc->funcMenu($headerSection, t3lib_BEfunc::getFuncMenu($this->id, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function'])));
			$this->content .= $this->doc->divider(5);

			// Render content:
			$this->moduleContent();

			// ShortCut
			if ($BE_USER->mayMakeShortcut() && !isset($_GET['export']))	{
				$this->content .= $this->doc->spacer(20) . $this->doc->section('', $this->doc->makeShortcutIcon('id', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name']));
			}

			if (!isset($_GET['export'])){
				$this->content .= $this->doc->spacer(10);
			}

		// If no access or if ID == zero
		} else {
			$this->doc = t3lib_div::makeInstance('bigDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content .= $this->doc->startPage($LANG->getLL('title'));
			$this->content .= $this->doc->header($LANG->getLL('title'));
			$this->content .= $this->doc->spacer(5);
			$this->content .= $this->doc->spacer(10);
		}
	}

	/**
	 * Setting up the config for the module menu
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;

		$this->MOD_MENU = array (
			'function' => array (
				'1' => '[icon_table]' . $LANG->getLL('function1'),
				'2' => '[icon_chart]' . $LANG->getLL('function2')
			)
		);

		parent::menuConfig();
	}

	/**
	 * Final output for backend module
	 *
	 * @return	void
	 */
	function printContent()	{
		if (!isset($_GET['export'])){
			$this->content .= $this->doc->endPage();
		}

		echo $this->stringReplace($this->content);
	}

	/**
	 * Dispatch method for backend module
	 *
	 * @return	void
	 */
	function moduleContent()	{
		global $BACK_PATH, $LANG;

		$this->lang = $LANG;
		$this->back_path = $BACK_PATH;
		$this->action = t3lib_div::makeInstance('tx_powermail_action');

		// A mail should be deleted
		$deleteID = t3lib_div::_GET('deleteID');
		if ($deleteID > 0) {
			$this->content .= $this->action->main(intval($deleteID), $this->lang); // Show export functions
		}
		$this->action->deleteFiles(); // delete old temp files from typo3temp folder

		switch ((string) $this->MOD_SETTINGS['function']) { // show function 1 or 2
			case 1: // powermail list view
			default:
				$mailID = t3lib_div::_GET('mailID');
				if (empty($mailID)) { // no mailID set in GET params

					$export = t3lib_div::_GET('export');
					if (empty($export)) { // no export
						$this->belist = t3lib_div::makeInstance('tx_powermail_belist');
						$this->belist->init($this->lang);
						$this->content .= $this->belist->main($this->id, $this->back_path); // Show list

					} else {
						$this->export = t3lib_div::makeInstance('tx_powermail_export');
						$this->content = $this->export->main($export, $this->id, $this->lang); // Show export functions
					}

				} else { // show only one with details
					$this->belist = t3lib_div::makeInstance('tx_powermail_belist');
					$this->belist->init($LANG);
					$this->content .= $this->belist->main($this->id, $this->back_path, intval($mailID)); // Show 1 intem of list

					$this->bedetails = t3lib_div::makeInstance('tx_powermail_bedetails');
					$this->content .= $this->bedetails->main(intval($mailID), $this->lang); // Show details
				}
			break;

			// Powermail chart view
			case 2:
				$this->charts = t3lib_div::makeInstance('tx_powermail_charts');
				$this->content .= $this->charts->main($this);
			break;
		}
	}

	/**
	 * Replaces markers with icons in module menu
	 *
	 * @param	string		$content
	 * @return	string
	 */
	function stringReplace($content) {
		$content = str_replace('>[icon_table]', ' style="background-image: url(../img/icon_select_table.gif); background-position: left 0; background-repeat: no-repeat; padding: 2px 0 2px 20px;">', $content); // add table icon
		$content = str_replace('>[icon_chart]', ' style="background-image: url(../img/icon_select_chart.gif); background-position: left 0; background-repeat: no-repeat; padding: 2px 0 2px 20px;">', $content); // add chart icon

		return $content;
	}

	/**
	 * Init method for backend module
	 *
	 * @return	void
	 */
	function init()	{
		global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

		parent::init();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_powermail_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE) include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();
?>