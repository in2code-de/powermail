<?php
namespace In2code\Powermail\Domain\Repository;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * FieldRepository
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class FieldRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * @param \array $uids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByUids($uids) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->matching(
			$query->in('uid', $uids)
		);
		return $query->execute();
	}

	/**
	 * Return uid from given field marker and form
	 *
	 * @param \string $marker
	 * @param \int $formUid
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByMarkerAndForm($marker, $formUid = 0) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectSysLanguage(FALSE);
		$query->matching(
			$query->logicalAnd(
				array(
					$query->equals('marker', $marker),
					$query->equals('pages.forms.uid', $formUid)
				)
			)
		);
		$query->setLimit(1);
		$result = $query->execute()->getFirst();
		return $result;
	}
}