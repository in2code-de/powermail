<?php
namespace In2code\Powermail\Domain\Repository;

use In2code\Powermail\Domain\Model\Answer;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

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
 *          GNU Lesser General Public License, version 3 or later
 */
class AnswerRepository extends AbstractRepository
{

    /**
     * Find single Answer by field uid and mail uid
     *
     * @param int $fieldUid
     * @param int $mailUid
     * @return Answer
     */
    public function findByFieldAndMail($fieldUid, $mailUid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $and = [
            $query->equals('mail', $mailUid),
            $query->equals('field', $fieldUid)
        ];

        $constraint = $query->logicalAnd($and);
        $query->matching($constraint);
        $query->setLimit(1);
        return $query->execute()->getFirst();
    }

    /**
     * Find answers with uploaded file
     *
     * @return QueryResultInterface
     */
    public function findByAnyUpload()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        // get all uploaded answers which are not empty
        $query->matching(
            $query->logicalAnd(
                [
                    $query->equals('valueType', 3),
                    $query->logicalNot($query->equals('value', ''))
                ]
            )
        );

        return $query->execute();
    }
}
