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
                    $query->equals('valueType', Answer::VALUE_TYPE_UPLOAD),
                    $query->logicalNot($query->equals('value', ''))
                ]
            )
        );

        return $query->execute();
    }
}
