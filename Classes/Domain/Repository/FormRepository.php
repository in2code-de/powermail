<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Repository;

use Doctrine\DBAL\Exception;
use In2code\Powermail\Database\QueryGenerator;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\DatabaseUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class FormRepository
 */
class FormRepository extends AbstractRepository
{
    /**
     * Find Form by given Page Uid
     *
     * @param int $uid page uid
     * @return ?Form
     */
    public function findByPages(int $uid): ?Form
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false)->setRespectSysLanguage(false);
        $query->matching($query->equals('pages.uid', $uid));
        /** @var Form $form */
        $form = $query->execute()->getFirst();
        return $form;
    }

    /**
     * Returns form with captcha from given UID
     *
     * @param Form $form
     * @return QueryResultInterface
     */
    public function hasCaptcha(Form $form): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false)->setRespectSysLanguage(false);
        $and = [
            $query->equals('uid', $form->getUid()),
            $query->equals('pages.fields.type', 'captcha'),
        ];
        $query->matching($query->logicalAnd(...$and));
        return $query->execute();
    }

    /**
     * Returns form with password from given UID
     *
     * @param Form $form
     * @return QueryResultInterface
     */
    public function hasPassword(Form $form): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $and = [
            $query->equals('uid', $form->getUid()),
            $query->equals('pages.fields.type', 'password'),
        ];
        $query->matching($query->logicalAnd(...$and));
        return $query->execute();
    }

    /**
     * This function is a workarround to get the field value of
     * "pages" in the table "forms"
     * (only relevant if IRRE was replaced by Element Browser)
     *
     * @param int $uid Form UID
     * @return string
     */
    public function getPagesValue(int $uid): string
    {
        $query = $this->createQuery();

        // create sql statement
        $sql = 'select pages';
        $sql .= ' from ' . Form::TABLE_NAME;
        $sql .= ' where uid = ' . (int)$uid;
        $sql .= ' limit 1';

        $result = $query->statement($sql)->execute(true);
        return $result[0]['pages'];
    }

    /**
     * Find all and don't respect Storage
     *
     * @return QueryResultInterface
     */
    public function findAll()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        return $query->execute();
    }

    /**
     * Find all within a Page and all subpages
     *
     * @param int $pid start page identifier
     * @return QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findAllInPidAndRootline(int $pid): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        if ($pid > 0) {
            $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
            $pids = GeneralUtility::trimExplode(',', $queryGenerator->getTreeList($pid, 20, 0, 1), true);
            $pids = BackendUtility::filterPagesForAccess($pids);
            $query->matching($query->in('pid', $pids));
        } else {
            if (!BackendUtility::isBackendAdmin()) {
                $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
                $pids = $pageRepository->getAllPages();
                $pids = BackendUtility::filterPagesForAccess($pids);
                $query->matching($query->in('pid', $pids));
            }
        }
        $query->setOrderings(['title' => QueryInterface::ORDER_ASCENDING]);

        return $query->execute();
    }

    /**
     * Find all localized records with
     *        tx_powermail_domain_model_form.pages = ""
     *
     * @return array
     */
    public function findAllWrongLocalizedForms(): array
    {
        $query = $this->createQuery();

        $sql = 'select uid,pid,title';
        $sql .= ' from ' . Form::TABLE_NAME;
        $sql .= ' where pages = \'\'';
        $sql .= ' and sys_language_uid > 0';
        $sql .= ' and deleted = 0';
        $sql .= ' limit 1';

        return $query->statement($sql)->execute(true);
    }

    /**
     * Fix wrong localized forms
     *
     * @return void
     */
    public function fixWrongLocalizedForms(): void
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME);
        $queryBuilder
            ->update(Form::TABLE_NAME)
            ->where('sys_language_uid > 0 and deleted = 0 and pages = \'\'')
            ->set('pages', 0)
            ->executeStatement();
    }

    /**
     * Get Fieldlist from Form UID
     *
     * @param int $formUid Form UID
     * @return array
     * @throws Exception
     */
    public function getFieldsFromFormWithSelectQuery(int $formUid): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Field::TABLE_NAME, true);
        $where = 'f.deleted = 0 and f.hidden = 0 and f.type != \'submit\' and f.sys_language_uid IN (-1,0)' .
            ' and fo.uid = ' . (int)$formUid;
        return $queryBuilder
            ->select('f.uid', 'f.title', 'f.sender_email', 'f.sender_name', 'f.marker')
            ->from(Field::TABLE_NAME, 'f')
            ->join('f', Page::TABLE_NAME, 'p', 'f.page = p.uid')
            ->join('p', Form::TABLE_NAME, 'fo', 'p.form = fo.uid')
            ->where($where)
            ->orderBy('f.sorting', 'asc')
            ->setMaxResults(10000)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * Get Field Uid List from given Form Uid
     *
     * @param int $formUid
     * @return array e.g. array(123, 234, 567)
     */
    public function getFieldUidsFromForm(int $formUid): array
    {
        $fields = [];
        $form = $this->findByUid($formUid);
        if ($form !== null) {
            /** @var Page $page */
            foreach ($form->getPages() as $page) {
                /** @var Field $field */
                foreach ($page->getFields() as $field) {
                    if ($field->isAdvancedFieldType()) {
                        $fields[] = $field->getUid();
                    }
                }
            }
        }
        return $fields;
    }
}
