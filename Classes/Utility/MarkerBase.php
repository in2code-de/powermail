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
 * Base Class for Backend-Marker functions
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class Tx_Powermail_Utility_MarkerBase {

	/**
	 * Array with all GET/POST params to save
	 *
	 * @var $data array
	 */
	protected $data;

	/**
	 * Marker Array
	 *
	 * @var array $marker
	 */
	protected $marker;

	/**
	 * Form Uid
	 *
	 * @var int $formUid
	 */
	protected $formUid;

	/**
	 * Existing Markers from Database to current form
	 *
	 * @var array $existingMarkers
	 */
	protected $existingMarkers;

	/**
	 * Clean Marker String ("My Field" => "my_field")
	 *
	 * @param	string		$string Any String
	 * @return	string
	 */
	protected function cleanString($string) {
		$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
		$string = str_replace(array('-'), '_', $string);
		$string = strtolower($string);
		return $string;
	}

	/**
	 * Read Form Uid from GET params
	 *
	 * return int form uid
	 */
	protected function getFormUid() {
		// if form is given in GET params (open form and pages and fields via IRRE)
		if (isset($data['tx_powermail_domain_model_forms']) && is_array($data['tx_powermail_domain_model_forms'])) {
			foreach (array_keys($this->data['tx_powermail_domain_model_forms']) as $uid) {
				return $uid;
			}
		}

		// if field is directly opened (no IRRE OR opened pages with fields via IRRE)
		if (isset($data['tx_powermail_domain_model_fields']) && is_array($data['tx_powermail_domain_model_fields'])) {
			foreach (array_keys($this->data['tx_powermail_domain_model_fields']) as $uid) {
				if (isset($this->data['tx_powermail_domain_model_fields'][$uid]['marker'])) {
					return $this->getFormUidFromFieldUid($uid);
				}
			}
		}

		return 0;
	}

	/**
	 * Get form uid from any of its field
	 *
	 * @param int $fieldUid
	 * @return int $formUid
	 */
	protected function getFormUidFromFieldUid($fieldUid) {
		$select = 'tx_powermail_domain_model_forms.uid';
		$from = '
			tx_powermail_domain_model_forms
			LEFT JOIN tx_powermail_domain_model_pages ON tx_powermail_domain_model_pages.forms = tx_powermail_domain_model_forms.uid
			LEFT JOIN tx_powermail_domain_model_fields ON tx_powermail_domain_model_fields.pages = tx_powermail_domain_model_pages.uid
		';
		$where = 'tx_powermail_domain_model_fields.uid = ' . intval($fieldUid);
		$groupBy = '';
		$orderBy = '';
		$limit = 1;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		if ($res) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			return $row['uid'];
		}
		return 0;
	}

	/**
	 * Get array with markers from a complete form
	 *
	 * @return array
	 */
	protected function getFieldMarkersFromForm() {
		$array = array();
		$select = 'tx_powermail_domain_model_fields.marker, tx_powermail_domain_model_fields.uid';
		$from = '
			tx_powermail_domain_model_forms
			LEFT JOIN tx_powermail_domain_model_pages ON tx_powermail_domain_model_pages.forms = tx_powermail_domain_model_forms.uid
			LEFT JOIN tx_powermail_domain_model_fields ON tx_powermail_domain_model_fields.pages = tx_powermail_domain_model_pages.uid
		';
		$where = 'tx_powermail_domain_model_forms.uid = ' . intval($this->formUid);
		$where .= ' and tx_powermail_domain_model_fields.deleted = 0';
		$groupBy = '';
		$orderBy = '';
		$limit = 1000;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		if ($res) {
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$array['_' . $row['uid']] = $row['marker'];
			}
		}
		return $array;
	}

	/**
	 * Make Array with unique values
	 *
	 * @param $array
	 * @return void
	 */
	protected function makeUniqueValueInArray(&$array) {
		$newArray = array();
		foreach ((array) $array as $key => $value) {
			if (!in_array($value, $newArray)) {
				$newArray[$key] = $value;
			} else {
				for ($i = 1; $i < 100; $i++) {
					// remove appendix "_xx"
					$value = preg_replace('/_[0-9][0-9]$/', '', $value);
					$value .= '_' . str_pad($i, 2, '0', STR_PAD_LEFT);
					if (!in_array($value, $newArray)) {
						$newArray[$key] = $value;
						break;
					}
				}
			}
		}
		$array = $newArray;
		unset($newArray);
	}

	/**
	 * Get marker values
	 *
	 * @return void
	 */
	protected function getMarkers() {
		$this->marker = array();
		foreach ((array) $this->data['tx_powermail_domain_model_fields'] as $fieldUid => $fieldValues) {
			if (!empty($fieldValues['title'])) {
				$this->marker['_' . $fieldUid] =
					(isset($fieldValues['marker']) ? $fieldValues['marker'] : $this->cleanString($fieldValues['title']));
			}
		}
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->data = t3lib_div::_GP('data');
		$this->getMarkers();
		$this->formUid = $this->getFormUid();
		$this->existingMarkers = $this->getFieldMarkersFromForm();
	}
}