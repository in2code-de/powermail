<?php
namespace In2code\Powermail\Domain\Repository;

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
 * AnswerRepository
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class AnswerRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * Find single Answer by field uid and mail uid
	 *
	 * @param int $fieldUid
	 * @param int $mailUid
	 * @return \In2code\Powermail\Domain\Model\Answer
	 */
	public function findByFieldAndMail($fieldUid, $mailUid) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		$and = array(
			$query->equals('mail', $mailUid),
			$query->equals('field', $fieldUid)
		);

		$constraint = $query->logicalAnd($and);
		$query->matching($constraint);
		$query->setLimit(1);
		return $query->execute()->getFirst();
	}

	/**
	 * Find any answer with uploaded file
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByAnyUpload() {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE);

		// get all uploaded answers which are not empty
		$query->matching(
			$query->logicalAnd(
				array(
					$query->equals('valueType', 3),
					$query->logicalNot($query->equals('value', ''))
				)
			)
		);

		return $query->execute();
	}
}