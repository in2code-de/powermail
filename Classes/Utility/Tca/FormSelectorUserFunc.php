<?php
namespace In2code\Powermail\Utility\Tca;

use \TYPO3\CMS\Core\Utility\GeneralUtility,
	\In2code\Powermail\Utility\Div;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * Powermail Form Selector
 * 		Used in FlexForm
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 *
 */
class FormSelectorUserFunc {

	/**
	 * Create Array for Form Selection
	 * 		Show all forms only from a pid and it's subpages:
	 * 			tx_powermail.flexForm.formSelection = 123
	 * 		Show all forms only from this pid and it's subpages:
	 * 			tx_powermail.flexForm.formSelection = current
	 * 		If no TSConfig set, all forms will be shown
	 *
	 * @param array $params
	 * @param object $pObj Parent Object
	 * @return void
	 */
	public function getForms(&$params, $pObj) {
		$typoScriptConfiguration = \TYPO3\CMS\Backend\Utility\BackendUtility::getPagesTSconfig(Div::getPidFromBackendPage());
		$language = $params['row']['sys_language_uid'];
		$startPid = 0;
		if (!empty($typoScriptConfiguration['tx_powermail.']['flexForm.']['formSelection'])) {
			$startPid = $typoScriptConfiguration['tx_powermail.']['flexForm.']['formSelection'];
		}
		$params['items'] = array();
		foreach ($this->getAllForms($startPid, $language) as $form) {
			$params['items'][] = array(
				$form['title'],
				$form['uid']
			);
		}
	}

	/**
	 * Get Forms from Database
	 *
	 * @param int|string $startPid Integer or "current"
	 * @param int $language
	 * @return array
	 */
	protected function getAllForms($startPid, $language) {
		$select = 'tx_powermail_domain_model_forms.uid, tx_powermail_domain_model_forms.title';
		$from = 'tx_powermail_domain_model_forms';
		$where = '
			tx_powermail_domain_model_forms.deleted = 0 and
			tx_powermail_domain_model_forms.hidden = 0 and
			(tx_powermail_domain_model_forms.sys_language_uid IN (-1,0) or
			(
				tx_powermail_domain_model_forms.l10n_parent = 0 and
				tx_powermail_domain_model_forms.sys_language_uid = ' . intval($language) . '
			)
			)';
		if (!empty($startPid)) {
			$where .= ' and pid in (' . $this->getPidListFromStartingPoint($startPid) . ')';
		}
		$groupBy = '';
		$orderBy = 'tx_powermail_domain_model_forms.title ASC';
		$limit = 10000;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

		$array = array();
		if ($res) {
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$array[] = $row;
			}
		}

		return $array;
	}

	/**
	 * Get commaseparated list of PID under a starting Page
	 *
	 * @param int|string $startPid Integer or "current"
	 * @return string
	 */
	protected function getPidListFromStartingPoint($startPid = 0) {
		/** @var \TYPO3\CMS\Core\Database\QueryGenerator $queryGenerator */
		$queryGenerator = GeneralUtility::makeInstance('\TYPO3\CMS\Core\Database\QueryGenerator');
		if ($startPid === 'current') {
			$startPid = Div::getPidFromBackendPage();
		}
		$list = $queryGenerator->getTreeList($startPid, 10, 0, 1);
		return $list;
	}
}