<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Repository;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\DatabaseUtility;
use In2code\Powermail\Utility\ObjectUtility;

/**
 * Class FieldRepository
 */
class FieldRepository extends AbstractRepository
{

    /**
     * Find all records from given uids and
     * respect the sorting
     *
     * @param array $uids
     * @return array
     */
    public function findByUids($uids)
    {
        $result = [];
        foreach ($uids as $uid) {
            $query = $this->createQuery();
            $query->getQuerySettings()->setRespectStoragePage(false);
            $query->getQuerySettings()->setRespectSysLanguage(false);
            $field = $query->matching($query->equals('uid', $uid))->execute()->getFirst();
            if ($field !== null) {
                $result[] = $field;
            }
        }
        return $result;
    }

    /**
     * Return uid from given field marker and form
     *
     * @param string $marker
     * @param int $formUid
     * @return Field
     */
    public function findByMarkerAndForm($marker, $formUid = 0)
    {
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            return $this->findByMarkerAndFormAlternative($marker, $formUid);
        }

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->matching(
            $query->logicalAnd(
                [
                    $query->equals('marker', $marker),
                    $query->equals('pages.forms.uid', $formUid)
                ]
            )
        );
        $query->setLimit(1);
        $result = $query->execute()->getFirst();
        return $result;
    }

    /**
     * Find all localized records with
     *        tx_powermail_domain_model_field.marker != ""
     *
     * @return mixed
     */
    public function findAllFieldsWithFilledMarkerrsInLocalizedFields()
    {
        $query = $this->createQuery();

        $sql = 'select uid,pid,title,marker,sys_language_uid';
        $sql .= ' from ' . Field::TABLE_NAME;
        $sql .= ' where marker != ""';
        $sql .= ' and sys_language_uid > 0';
        $sql .= ' and deleted = 0';
        $sql .= ' limit 1';

        $result = $query->statement($sql)->execute(true);

        return $result;
    }

    /**
     * Find all localized records with
     *        tx_powermail_domain_model_field.pages = "0"
     *
     * @return array
     */
    public function findAllWrongLocalizedFields()
    {
        $pages = [];
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Field::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('uid', 'pid', 'title', 'l10n_parent', 'sys_language_uid')
            ->from(Field::TABLE_NAME)
            ->where('(pages = "" or pages = 0) and sys_language_uid > 0 and deleted = 0')
            ->execute()
            ->fetchAll();
        foreach ($rows as $row) {
            $pages[] = $row;
        }
        return $pages;
    }

    /**
     * Get parent page uid form given field uid
     *
     * @param int $fieldUid
     * @return int
     */
    protected function getPageUidFromFieldUid($fieldUid)
    {
        $query = $this->createQuery();
        $sql = 'select pages';
        $sql .= ' from ' . Field::TABLE_NAME;
        $sql .= ' where uid = ' . (int)$fieldUid;
        $sql .= ' and deleted = 0';
        $sql .= ' limit 1';
        $row = $query->statement($sql)->execute(true);
        return (int)$row[0]['pages'];
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @return int
     */
    protected function getLocalizedPageUidFromPageUid($pageUid, $sysLanguageUid)
    {
        $query = $this->createQuery();
        $sql = 'select uid';
        $sql .= ' from ' . Page::TABLE_NAME;
        $sql .= ' where l10n_parent = ' . (int)$pageUid;
        $sql .= ' and sys_language_uid = ' . (int)$sysLanguageUid;
        $sql .= ' and deleted = 0';
        $row = $query->statement($sql)->execute(true);
        return (int)$row[0]['uid'];
    }

    /**
     * Return uid from given field marker and form (if no IRRE)
     *
     * @param string $marker
     * @param int $formUid
     * @return Field
     */
    protected function findByMarkerAndFormAlternative($marker, $formUid = 0)
    {
        // get pages from form
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        $form = $formRepository->findByUid($formUid);
        $pageUids = [];
        foreach ($form->getPages() as $page) {
            $pageUids[] = $page->getUid();
        }

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->matching(
            $query->logicalAnd(
                [
                    $query->equals('marker', $marker),
                    $query->in('pages', $pageUids)
                ]
            )
        );
        return $query->setLimit(1)->execute()->getFirst();
    }

    /**
     * Return type from given field marker and form
     *
     * @param string $marker Field marker
     * @param integer $formUid Form UID
     * @return string Field Type
     */
    public function getFieldTypeFromMarker($marker, $formUid = 0)
    {
        $field = $this->findByMarkerAndForm($marker, $formUid);
        if (method_exists($field, 'getType')) {
            return $field->getType();
        }
        return '';
    }

    /**
     * Return uid from given field marker and form
     *
     * @param string $marker Field marker
     * @param integer $formUid Form UID
     * @return int Field UID
     */
    public function getFieldUidFromMarker($marker, $formUid = 0)
    {
        $field = $this->findByMarkerAndForm($marker, $formUid);
        if (method_exists($field, 'getUid')) {
            return $field->getUid();
        }
        return 0;
    }

    /**
     * @param int $uid
     * @return string
     */
    public function getMarkerFromUid(int $uid): string
    {
        $marker = '';
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Field::TABLE_NAME);
        $rows =
            $queryBuilder->select('marker')->from(Field::TABLE_NAME)->where('uid=' . (int)$uid)->execute()->fetchAll();
        if (!empty($rows[0]['marker'])) {
            $marker = $rows[0]['marker'];
        }
        return $marker;
    }

    /**
     * @param int $uid
     * @return string
     */
    public function getTypeFromUid(int $uid): string
    {
        $type = '';
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Field::TABLE_NAME);
        $rows =
            $queryBuilder->select('type')->from(Field::TABLE_NAME)->where('uid=' . (int)$uid)->execute()->fetchAll();
        if (!empty($rows[0]['type'])) {
            $type = $rows[0]['type'];
        }
        return $type;
    }
}
