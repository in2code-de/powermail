<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Repository;

use In2code\Powermail\Domain\Model\User;
use In2code\Powermail\Utility\DatabaseUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

/**
 * Class UserRepository
 */
class UserRepository extends AbstractRepository
{

    /**
     * Find FE_Users by their group
     *
     * @param int $uid fe_groups UID
     * @return QueryResult
     */
    public function findByUsergroup($uid)
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
    public function findByUid($uid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->equals('uid', $uid));
        return $query->execute()->getFirst();
    }

    /**
     * Return usergroups uid of a given fe_user
     *
     * @param string $uid FE_user UID
     * @return array Usergroups
     */
    public function getUserGroupsFromUser($uid): array
    {
        $groups = [];
        $sql = 'select fe_groups.uid from fe_users, fe_groups, sys_refindex where';
        $sql .= ' sys_refindex.tablename = "fe_users" and sys_refindex.ref_table = "fe_groups"';
        $sql .= ' and fe_users.uid = sys_refindex.recuid and fe_groups.uid = sys_refindex.ref_uid';
        $sql .= ' and fe_users.uid = ' . (int)$uid;
        $connection = DatabaseUtility::getConnectionForTable('fe_groups');
        $rows = $connection->query($sql)->fetchAll();
        foreach ($rows as $row) {
            if (!empty($row['uid'])) {
                $groups[] = $row['uid'];
            }
        }
        return $groups;
    }
}
