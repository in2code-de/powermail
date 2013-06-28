<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Powermail Field Selector for Pi2 (powermail_frontend)
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Powermail_Utility_FieldSelectorUserFunc {

	/**
	 * Crazy Query Limit
	 *
	 * @var int
	 */
	protected $limit = 10000;

	/**
	 * Cretae Array for Field Selector
	 *
	 * @param	array	Config Array
	 * @param	object	Parent Object
	 * @return	void
	 */
	public function getFieldSelection(&$params, $pObj) {
		$formUid = $this->getFormFromFlexform($params);
		if (!$formUid) {
			$params['items'] = array(
				array(
					'Please select a form (Main Settings)',
					''
				)
			);
			return;
		}

		$this->getFieldsFromForm($formUid);

		foreach ((array) $this->getFieldsFromForm($formUid) as $field) {
			$params['items'][] = array(
				$field['title'] . ' {' . $field['marker'] . '}',
				$field['uid']
			);
		}
	}
	
	/**
	 * Return Form Uid from Flexform settings
	 * 
	 * @param	array	Config Array
	 * @return	int		Form Uid
	 */
	protected function getFormFromFlexform($params) {
		$xml = $params['row']['pi_flexform'];
		$flexform = t3lib_div::xml2array($xml);
		if (is_array($flexform) && isset($flexform['data']['main']['lDEF']['settings.flexform.main.form']['vDEF'])) {
			return $flexform['data']['main']['lDEF']['settings.flexform.main.form']['vDEF'];
		}
		return 0;
	}

	/**
	 * Get Fieldlist from Form UID
	 *
	 * @param integer $formUid Form UID
	 * @return array
	 */
	protected function getFieldsFromForm($formUid) {
		$select = 'tx_powermail_domain_model_fields.uid, tx_powermail_domain_model_fields.title, tx_powermail_domain_model_fields.marker';
		$from = '
			tx_powermail_domain_model_fields
			left join tx_powermail_domain_model_pages on tx_powermail_domain_model_fields.pages = tx_powermail_domain_model_pages.uid
			left join tx_powermail_domain_model_forms on tx_powermail_domain_model_pages.forms = tx_powermail_domain_model_forms.uid
		';
		$where = '
			tx_powermail_domain_model_fields.deleted = 0 and
			tx_powermail_domain_model_fields.hidden = 0 and
			tx_powermail_domain_model_fields.type != "submit" and
			tx_powermail_domain_model_fields.sys_language_uid IN (-1,0) and
			tx_powermail_domain_model_forms.uid = ' . intval($formUid);
		$groupBy = '';
		$orderBy = 'tx_powermail_domain_model_fields.sorting ASC';
		$limit = $this->limit;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

		$array = array();
		if ($res) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$array[] = $row;
			}
		}

		return $array;
	}
}
?>