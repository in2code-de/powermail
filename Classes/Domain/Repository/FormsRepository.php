<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 in2code GmbH <info@in2code.de>
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
 * FormsRepository
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class Tx_Powermail_Domain_Repository_FormsRepository extends Tx_Extbase_Persistence_Repository {

	/**
	 * Find Form objects by its given uids
	 *
	 * @param string $uids commaseparated list of uids
	 * @return Tx_Extbase_Persistence_QueryResult
	 */
	public function findByUids($uids) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectSysLanguage(FALSE);

		$query->matching(
			$query->in('uid', t3lib_div::trimExplode(',', $uids, 1))
		);

		$result = $query->execute();
		return $result;
	}

	/**
	 * Returns form with captcha from given UID
	 *
	 * @param int $uid Form Uid
	 * @return Tx_Extbase_Persistence_QueryResult
	 */
	public function hasCaptcha($uid) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE); // disable storage pid

		$and = array(
			$query->equals('uid', $uid),
			$query->equals('pages.fields.type', 'captcha')
		);

		$constraint = $query->logicalAnd($and); // create where object with AND
		$query->matching($constraint); // use constraint

		$result = $query->execute();
		return $result;
	}

	/**
	 * This function is a workarround to get the field value of "pages" in the table "forms" (only relevant if IRRE was replaced by Element Browser)
	 *
	 * @param int $uid Form UID
	 * @return string
	 */
	public function getPagesValue($uid) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setReturnRawQueryResult(true);

		// create sql statement
		$sql = 'select pages';
		$sql .= ' from tx_powermail_domain_model_forms';
		$sql .= ' where uid = ' . intval($uid);
		$sql .= ' limit 1';

		$result = $query->statement($sql)->execute();

		return $result[0]['pages'];
	}
}

?>