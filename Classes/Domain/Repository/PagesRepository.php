<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 in2code GmbH <info@in2code.de>, in2code.de
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
 *
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Powermail_Domain_Repository_PagesRepository extends Tx_Extbase_Persistence_Repository {

	/**
	 * Get title from table "pages" in TYPO3
	 *
	 * @param $uid
	 */
	public function getPageNameFromUid($uid) {
		$query = $this->createQuery();

		// create sql statement
		$sql = 'select title';
		$sql .= ' from pages';
		$sql .= ' where uid = ' . intval($uid);
		$sql .= ' limit 1';

		$query->getQuerySettings()->setReturnRawQueryResult(true); //this generates an array and makes it much slower
		$result = $query->statement($sql)->execute();

		return $result[0]['title'];
	}
}

?>