<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Repository;

use In2code\Powermail\Domain\Model\User;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class UserRepository
 */
class UserRepository extends AbstractRepository
{
    /**
     * @param int $uid
     * @return QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByUsergroup(int $uid): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->contains('usergroup', $uid));
        return $query->execute();
    }

    /**
     * Find by Uid but don't respect storage page
     *
     * @param int $uid
     * @return User
     */
    public function findByUid($uid): User
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->equals('uid', $uid));
        /** @var User $user */
        $user = $query->execute()->getFirst();
        return $user;
    }

    /**
     * @param string $uid FE_user UID
     * @return int[]
     */
    public function getUserGroupsFromUser($uid): array
    {
        $groups = [];
        $sql = 'select fe_groups.uid from fe_users, fe_groups, sys_refindex where';
        $sql .= ' sys_refindex.tablename = "fe_users" and sys_refindex.ref_table = "fe_groups"';
        $sql .= ' and fe_users.uid = sys_refindex.recuid and fe_groups.uid = sys_refindex.ref_uid';
        $sql .= ' and fe_users.uid = ' . (int)$uid;

        $query = $this->createQuery();
        $rows = $query->statement($sql)->execute(true);
        foreach ($rows as $row) {
            if (!empty($row['uid'])) {
                $groups[] = (int)$row['uid'];
            }
        }
        return $groups;
    }
}
