<?php
namespace In2code\Powermail\Utility\Tca;

use \TYPO3\CMS\Core\Utility\GeneralUtility,
	\In2code\Powermail\Utility\Div;

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
 * 		Used in FlexForm
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 *
 */
class FieldSelectorUserFunc {

	/**
	 * Cretae Array for Field Selector
	 *
	 * @param array $params
	 * @param object $pObj Parent Object
	 * @return void
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

		foreach ((array) Div::getFieldsFromFormWithSelectQuery($formUid) as $field) {
			$params['items'][] = array(
				$field['title'] . ' {' . $field['marker'] . '}',
				$field['uid']
			);
		}
	}

	/**
	 * Return Form Uid from Flexform settings
	 * 
	 * @param array $params
	 * @return int
	 */
	protected function getFormFromFlexform($params) {
		$xml = $params['row']['pi_flexform'];
		$flexform = GeneralUtility::xml2array($xml);
		if (is_array($flexform) && isset($flexform['data']['main']['lDEF']['settings.flexform.main.form']['vDEF'])) {
			return $flexform['data']['main']['lDEF']['settings.flexform.main.form']['vDEF'];
		}
		return 0;
	}
}