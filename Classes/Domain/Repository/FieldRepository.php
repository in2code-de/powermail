<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Repository;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\DatabaseUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

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
    public function findByUids(array $uids): array
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
     * @throws Exception
     * @throws InvalidQueryException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function findByMarkerAndForm(string $marker, int $formUid = 0): ?Field
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
                    $query->equals('page.form.uid', $formUid)
                ]
            )
        );
        $query->setLimit(1);
        /** @var Field $field */
        $field = $query->execute()->getFirst();
        return $field;
    }

    /**
     * Find all localized records with
     *        tx_powermail_domain_model_field.marker != ""
     *
     * @return QueryResultInterface
     */
    public function findAllFieldsWithFilledMarkerrsInLocalizedFields(): QueryResultInterface
    {
        $query = $this->createQuery();

        $sql = 'select uid,pid,title,marker,sys_language_uid';
        $sql .= ' from ' . Field::TABLE_NAME;
        $sql .= ' where marker != ""';
        $sql .= ' and sys_language_uid > 0';
        $sql .= ' and deleted = 0';
        $sql .= ' limit 1';

        return $query->statement($sql)->execute(true);
    }

    /**
     * Find all localized records with
     *        tx_powermail_domain_model_field.page = "0"
     *
     * @return array
     */
    public function findAllWrongLocalizedFields(): array
    {
        $pages = [];
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Field::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('uid', 'pid', 'title', 'l10n_parent', 'sys_language_uid')
            ->from(Field::TABLE_NAME)
            ->where('(page = "" or page = 0) and sys_language_uid > 0 and deleted = 0')
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
    protected function getPageUidFromFieldUid(int $fieldUid): int
    {
        $query = $this->createQuery();
        $sql = 'select page';
        $sql .= ' from ' . Field::TABLE_NAME;
        $sql .= ' where uid = ' . (int)$fieldUid;
        $sql .= ' and deleted = 0';
        $sql .= ' limit 1';
        $row = $query->statement($sql)->execute(true);
        return (int)$row[0]['page'];
    }

    /**
     * @param int $pageUid
     * @param int $sysLanguageUid
     * @return int
     */
    protected function getLocalizedPageUidFromPageUid(int $pageUid, int $sysLanguageUid): int
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
     * @throws Exception
     * @throws InvalidQueryException
     */
    protected function findByMarkerAndFormAlternative(string $marker, int $formUid = 0): ?Field
    {
        // get pages from form
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        $form = $formRepository->findByUid($formUid);
        $pageIdentifiers = [];
        /** @var Page $page */
        foreach ($form->getPages() as $page) {
            $pageIdentifiers[] = $page->getUid();
        }

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->matching(
            $query->logicalAnd(
                [
                    $query->equals('marker', $marker),
                    $query->in('page', $pageIdentifiers)
                ]
            )
        );
        /** @var Field $field */
        $field = $query->setLimit(1)->execute()->getFirst();
        return $field;
    }

    /**
     * Return type from given field marker and form
     *
     * @param string $marker Field marker
     * @param int $formUid Form UID
     * @return string Field Type
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     */
    public function getFieldTypeFromMarker(string $marker, int $formUid = 0): string
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
     * @param int $formUid Form UID
     * @return int Field UID
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     */
    public function getFieldUidFromMarker(string $marker, int $formUid = 0): int
    {
        $field = $this->findByMarkerAndForm($marker, $formUid)??'';
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
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Field::TABLE_NAME);
        return (string)$queryBuilder
            ->select('marker')
            ->from(Field::TABLE_NAME)
            ->where('uid=' . (int)$uid)
            ->setMaxResults(1)
            ->execute()
            ->fetchColumn();
    }

    /**
     * @param int $uid
     * @return string
     */
    public function getTypeFromUid(int $uid): string
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Field::TABLE_NAME);
        return (string)$queryBuilder
            ->select('type')
            ->from(Field::TABLE_NAME)
            ->where('uid=' . (int)$uid)
            ->setMaxResults(1)
            ->execute()
            ->fetchColumn();
    }
}
