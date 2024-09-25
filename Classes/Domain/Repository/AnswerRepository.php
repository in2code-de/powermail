<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Repository;

use In2code\Powermail\Domain\Model\Answer;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class AnswerRepository
 */
class AnswerRepository extends AbstractRepository
{
    /**
     * Find single Answer by field uid and mail uid
     *
     * @param int $fieldUid
     * @param int $mailUid
     * @return Answer|null
     */
    public function findByFieldAndMail($fieldUid, $mailUid): ?Answer
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $and = [
            $query->equals('mail', $mailUid),
            $query->equals('field', $fieldUid),
        ];
        $query->matching($query->logicalAnd(...$and));
        $query->setLimit(1);
        /** @var Answer $answer */
        $answer = $query->execute()->getFirst();
        return $answer;
    }

    /**
     * Find answers with uploaded file
     *
     * @return QueryResultInterface
     */
    public function findByAnyUpload(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        // get all uploaded answers which are not empty
        $and = [
            $query->equals('valueType', Answer::VALUE_TYPE_UPLOAD),
            $query->logicalNot($query->equals('value', '')),
        ];
        $query->matching($query->logicalAnd(...$and));
        return $query->execute();
    }
}
