<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 in2code GmbH <info@in2code.de>
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
 * Context Repository for Mails
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class Tx_Powermail_Domain_Repository_MailsBasicRepository extends Tx_Powermail_Domain_Repository_MailsRepository {

	/**
	 * Find Mails by its uids
	 *
	 * @param Tx_Extbase_Persistence_QueryResult $mails
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findMailsByUids(Tx_Extbase_Persistence_QueryResult $mails) {
		$uidList = array();
		foreach ($mails as $mail) {
			$uidList[] = $mail->getUid();
		}

		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$this->ignoreEnableFields($query);

		if (count($uidList)) {
			$query->matching(
				$query->in('uid', $uidList)
			);
		}
		return $query->execute();
	}
}