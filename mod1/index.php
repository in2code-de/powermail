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

unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH . 'init.php');
require_once($BACK_PATH . 'template.php');

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

	function init()	{
		global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
		parent::init();
	}


	/**
	 * Main method of be module
	 * Generates header and module menu
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

		$this->LANG = $LANG;

		$PATH_TYPO3 = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . 'typo3/';

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
		$this->tsconfig = t3lib_BEfunc::getModTSconfig($this->id, 'tx_powermail_mod1');

		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id)) {

			if(!t3lib_div::_GP('export')) {

				$this->perpage = 25;
				// Get hits per page if set by tsconfig
				if($this->tsconfig['properties']['config.']['list.']['perPage'] > 0) {
					$this->perpage = intval($this->tsconfig['properties']['config.']['list.']['perPage']);
				}

				// Draw the header.
				$this->doc = t3lib_div::makeInstance('template');
				$this->doc->backPath = $BACK_PATH;
				$this->pageRenderer = $this->doc->getPageRenderer();

				// Add CSS for backend modul
				$this->pageRenderer->addCssFile( $BACK_PATH . t3lib_extMgm::extRelPath('powermail') .  'res/css/powermail_backend.css' );

				// Include Ext JS stuff
				$this->pageRenderer->loadExtJS();
				$this->pageRenderer->enableExtJSQuickTips();
				$this->pageRenderer->addJsFile($BACK_PATH . t3lib_extMgm::extRelPath('powermail') . 'res/js/Ext.ux.plugin.PagingToolbarResizer.js');
				$this->pageRenderer->addJsFile($BACK_PATH . t3lib_extMgm::extRelPath('powermail') . 'res/js/Ext.ux.plugin.FitToParent.js');
				$this->pageRenderer->addJsFile($BACK_PATH . t3lib_extMgm::extRelPath('powermail') . 'res/js/Ext.ux.form.DateTime.js');
				$this->pageRenderer->addJsFile($BACK_PATH . t3lib_extMgm::extRelPath('powermail') . 'res/js/Ext.grid.RowExpander.js');
				$this->pageRenderer->addJsFile($BACK_PATH . t3lib_extMgm::extRelPath('powermail') . 'res/js/Ext.ux.LinkButton.js');
				$this->pageRenderer->addJsFile($BACK_PATH . t3lib_extMgm::extRelPath('powermail') . 'res/js/powermail_backend.js');

				// Enable debug mode for Ext JS
				$this->pageRenderer->enableExtJsDebug();

				// Include Ext JS inline code
				$this->pageRenderer->addJsInlineCode('Powermail_Overview',"

	Ext.namespace('Powermail');

	// Parameter definition
	Powermail.statics = {
		'pagingSize': " . $this->perpage . ",
		'pid': " . $this->id .",
		'sort': 'crdate',
		'dir': 'DESC',
		'filterIcon': '" . t3lib_iconWorks::getSpriteIcon('actions-system-tree-search-open') . "',
		'renderTo': 'tx_powermail-grid',
		'ajaxController': '" . $this->doc->backPath . "ajax.php?ajaxID=tx_powermail::controller',
		'excelIcon': '" . t3lib_iconWorks::getSpriteIcon('mimetypes-excel') . "',
		'csvIcon': '" . t3lib_iconWorks::getSpriteIcon('mimetypes-text-csv') . "',
		'htmlIcon': '" . t3lib_iconWorks::getSpriteIcon('mimetypes-text-html') . "',
		'pdfIcon': '" . t3lib_iconWorks::getSpriteIcon('mimetypes-pdf') . "',
		'shortcutLink': '" . addslashes($this->doc->makeShortcutIcon('id', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name'])) . "',
		'deleteIcon': '" . t3lib_iconWorks::getSpriteIcon('actions-edit-delete') . "',
		'startDateTime': 0,
	 	'endDateTime': 0
	};

	// Localisation:
	Powermail.lang = {
		'title': 'Powermail',
		'path': 'Pfad',
		'loadMessage': 'Bitte warten...<br \/>Datens\u00e4tze werden geladen!',
		'deleteButton_text': 'L\u00f6schen',
		'deleteButton_tooltip': 'Ausgew\u00e4hlte Datens\u00e4tze l\u00f6schen',
		'error_NoSelectedRows_title': 'Keine Zeile ausgew\u00e4hlt',
		'error_NoSelectedRows_msg': 'Sie m\u00fcssen eine Zeile ausw\u00e4hlen!',
		'yes': 'Ja',
		'no': 'Nein',
		'crdate': 'Erstellt',
		'title_delete': 'L\u00f6schen?',
		'text_delete': 'Ausgew\u00e4hlte Datens\u00e4tze wirklich l\u00f6schen?',
		'pagingMessage': 'Anzeigen der Datens\u00e4tze {0} - {1} von {2}',
		'pagingEmpty': 'Keine Datens\u00e4tze anzuzeigen',
		'records': 'Datens\u00e4tze',
		'recordsPerPage': 'Datens\u00e4tze pro Seite',
		'createShortcut': 'Create a shortcut to this page',
		'exportAs': 'Export als:',
		'exportPdfText': 'Export in PDF format',
		'exportHtmlText': 'Export in HTML format',
		'exportCsvText': 'Export in CSV format',
		'exportExcelText': 'Export in Excel format',
		'filterBegin': 'Beginn:',
		'filterEnd': 'Ende:',
		'piVars': 'piVars',
		'date': 'Datum',
		'sender': 'Absender',
		'receiver': 'Empf\u00e4nger',
		'senderIP': 'Absender-IP'
	};
				");

				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= '
		<div id="typo3-docheader">
			<div id="typo3-docheader-row1"></div>
			<div id="typo3-docheader-row2">
				<div class="docheader-row2-left"><div class="docheader-funcmenu"></div></div>
				<div class="docheader-row2-right">' . $LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path') . ': <strong>' . t3lib_div::fixed_lgd_cs($this->pageinfo['_thePath'], -50) . '</strong></div>
			</div>
		</div>
		<div id="typo3-inner-docbody">
			<h4 class="uppercase">Powermail</h4>
			<div id="tx_powermail-grid"></div>
			<div id="label-grid"></div>
		</div>';

				$this->doc->form = '';
				$this->content .= $this->doc->endPage();

			} else {
				$this->export = t3lib_div::makeInstance('tx_powermail_export');
				$this->export->LANG = $this->LANG;
				$this->export->pid = t3lib_div::_GP('pid');
				$this->export->title = $this->pageinfo['_thePath'];
				$this->export->startDateTime = t3lib_div::_GP('startDateTime');
				$this->export->endDateTime = t3lib_div::_GP('endDateTime');
				$this->export->export = t3lib_div::_GP('export');
				$this->content = $this->export->main();
			}
		} else {
			// If no access or if ID == zero
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
		echo $this->content;
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