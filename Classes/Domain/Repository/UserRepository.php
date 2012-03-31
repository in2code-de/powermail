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
 * FE_User Repository
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class Tx_Powermail_Domain_Repository_UserRepository extends Tx_Extbase_Persistence_Repository {

	/**
	 * Find FE_Users by their group
	 *
	 * @param 	$uid	int		fe_groups UID
	 * @return	query result
	 */
	public function findByUsergroup($uid) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE); // disable storage pid

		$and = array(
			$query->greaterThan('uid', 0), // always true like 1=1
			$query->contains('usergroup', $uid)
		);

		$constraint = $query->logicalAnd($and); // create where object with AND
		$query->matching($constraint); // use constraint

		$result = $query->execute();
		return $result;
	}
}

?>