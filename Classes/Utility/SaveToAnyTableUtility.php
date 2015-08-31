<?php
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\SaveToAnyTableService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * SaveToAnyTable Utility Class
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class SaveToAnyTableUtility {

	/**
	 * Preflight to save values to any table in TYPO3 database
	 *
	 * @param Mail $mail
	 * @param array $conf TypoScript Configuration
	 * @return void
	 */
	public static function preflight($mail, $conf) {
		if (empty($conf['dbEntry.'])) {
			return;
		}
		/** @var ObjectManager $objectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var ConfigurationManager $configurationManager */
		$configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
		$contentObject = $configurationManager->getContentObject();
		$mailRepository = $objectManager->get('In2code\\Powermail\\Domain\\Repository\\MailRepository');
		$startArray = $mailRepository->getVariablesWithMarkersFromMail($mail);

		// one loop per table
		foreach ((array) array_keys($conf['dbEntry.']) as $table) {
			$contentObject->start($startArray);
			$table = substr($table, 0, -1);
			$dbEntryConfiguration = $conf['dbEntry.'][$table . '.'];
			$enable = $contentObject->cObjGetSingle($dbEntryConfiguration['_enable'], $conf['dbEntry.'][$table . '.']['_enable.']);
			if (!$enable) {
				continue;
			}

			/* @var $saveToAnyTable SaveToAnyTableService */
			$saveToAnyTableService = $objectManager->get('In2code\\Powermail\\Domain\\Service\\SaveToAnyTableService', $table);
			if (!empty($dbEntryConfiguration['_ifUnique.'])) {
				$uniqueFields = array_keys($dbEntryConfiguration['_ifUnique.']);
				$saveToAnyTableService->setMode($dbEntryConfiguration['_ifUnique.'][$uniqueFields[0]]);
				$saveToAnyTableService->setUniqueField($uniqueFields[0]);
				if (!empty($conf['dbEntry.'][$table . '.']['_ifUniqueWhereClause'])) {
					$saveToAnyTableService->setAdditionalWhereClause($conf['dbEntry.'][$table . '.']['_ifUniqueWhereClause']);
				}
			}

			// one loop per field
			foreach ((array) array_keys($conf['dbEntry.'][$table . '.']) as $field) {
				if (stristr($field, '.') || $field[0] === '_') {
					continue;
				}
				$value = $contentObject->cObjGetSingle($dbEntryConfiguration[$field], $dbEntryConfiguration[$field . '.']);
				$saveToAnyTableService->addProperty($field, $value);
			}
			if (!empty($conf['debug.']['saveToTable'])) {
				$saveToAnyTableService->setDevLog(TRUE);
			}
			$uid = $saveToAnyTableService->execute();
			$startArray = array_merge($startArray, array('uid_' . $table => $uid));
		}
	}
}