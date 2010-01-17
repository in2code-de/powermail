<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Alexander Kellner <alexander.kellner@einpraegsam.net>
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


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH . 'init.php');
require_once($BACK_PATH . 'template.php');
require_once('class.tx_powermail_belist.php'); // include Backend list function
require_once('class.tx_powermail_export.php'); // include Backend export function
require_once('class.tx_powermail_bedetails.php'); // include Backend detail function
require_once('class.tx_powermail_action.php'); // include Backend detail function
require_once('class.tx_powermail_charts.php'); // include Backend charts function

$LANG->includeLLFile('EXT:powermail/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF, 1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'Powermail' for the 'powermail' extension.
 *
 * @author	Mischa Heißmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@wunschtacho.de>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class tx_powermail_module1 extends t3lib_SCbase {
	var $pageinfo;
	
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
	
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
	
		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{
	
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
	
			$headerSection = $this->doc->getHeader('pages', $this->pageinfo, $this->pageinfo['_thePath']) . '<br />' . $LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path') . ': ' . t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'], 50);
	
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
	
			if (!isset($_GET['export'])) $this->content .= $this->doc->spacer(10);
		} else {
				// If no access or if ID == zero
	
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;
	
			$this->content .= $this->doc->startPage($LANG->getLL('title'));
			$this->content .= $this->doc->header($LANG->getLL('title'));
			$this->content .= $this->doc->spacer(5);
			$this->content .= $this->doc->spacer(10);
		}
	}
	
	// Dropdown for menu
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = array (
			'function' => array (
				'1' => '[icon_table]' . $LANG->getLL('function1'),
				'2' => '[icon_chart]' . $LANG->getLL('function2')
				//'3' => $LANG->getLL('function3'),
			)
		);
		parent::menuConfig();
	}
	
	// Final output
	function printContent()	{
		if (!isset($_GET['export'])) $this->content .= $this->doc->endPage(); // not needed for export
		echo $this->stringReplace($this->content);
	}
	
	// What to show
	function moduleContent()	{
		global $BACK_PATH,$LANG;
		$this->lang = $LANG;
		$this->back_path = $BACK_PATH;
		$this->action = t3lib_div::makeInstance('tx_powermail_action');
		
		if ($_GET['deleteID'] > 0) { // a mail should be deleted
			$this->content .= $this->action->main(intval($_GET['deleteID']), $LANG); // Show export functions
		}
		$this->action->deleteFiles(); // delete old temp files from typo3temp folder
		
		switch ((string) $this->MOD_SETTINGS['function']) { // show function 1 or 2
			case 1: // powermail list view
			default:
				if (empty($_GET['mailID'])) { // no mailID set in GET params
					if (empty($_GET['export'])) { // no export
						$this->belist = t3lib_div::makeInstance('tx_powermail_belist');
						$this->belist->init($LANG);
						$this->content .= $this->belist->main($this->id, $BACK_PATH); // Show list
					} else {
						$this->export = t3lib_div::makeInstance('tx_powermail_export');
						$this->content = $this->export->main($_GET['export'], $this->id, $LANG); // Show export functions
					}
				} else { // show only one with details
					$this->belist = t3lib_div::makeInstance('tx_powermail_belist');
					$this->belist->init($LANG);
					$this->content .= $this->belist->main($this->id, $BACK_PATH, intval($_GET['mailID'])); // Show 1 intem of list
					
					$this->bedetails = t3lib_div::makeInstance('tx_powermail_bedetails');
					$this->content .= $this->bedetails->main(intval($_GET['mailID']), $LANG); // Show details
				}
			break;
			case 2: // powermail chart view
				$this->charts = t3lib_div::makeInstance('tx_powermail_charts');
				$this->content .= $this->charts->main($this);
			break;
		}
	}
	
	
	// replace string with style
	function stringReplace($content) {
		$content = str_replace('>[icon_table]', ' style="background-image: url(../img/icon_select_table.gif); background-position: left 0; background-repeat: no-repeat; padding: 2px 0 2px 20px;">', $content); // add table icon
		$content = str_replace('>[icon_chart]', ' style="background-image: url(../img/icon_select_chart.gif); background-position: left 0; background-repeat: no-repeat; padding: 2px 0 2px 20px;">', $content); // add chart icon
		return $content;
	}
	
	
	// make variables global available
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
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