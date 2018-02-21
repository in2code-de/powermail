<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Repository;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Utility\DatabaseUtility;

/**
 * Class PageRepository
 */
class PageRepository extends AbstractRepository
{

    /**
     * @param int $uid
     * @return string
     */
    public function getPageNameFromUid($uid): string
    {
        $pageName = '';
        $query = $this->createQuery();
        $sql = 'select uid,title from pages where uid = ' . (int)$uid . ' limit 1';
        $result = $query->statement($sql)->execute(true);
        if (!empty($result[0]['title'])) {
            $pageName = $result[0]['title'];
        }
        return $pageName;
    }

    /**
     * Get properties from table "pages"
     *
     * @param int $uid
     * @return array
     */
    public function getPropertiesFromUid($uid)
    {
        $query = $this->createQuery();
        $sql = 'select * from pages where uid = ' . (int)$uid . ' limit 1';
        $result = $query->statement($sql)->execute(true);
        return $result[0];
    }

    /**
     * Get all pages with tt_content with a Powermail Plugin
     *
     * @param Form $form
     * @return array
     */
    public function getPagesWithContentRelatedToForm($form)
    {
        $query = $this->createQuery();

        $searchString = '%<field index=\"settings.flexform.main.form\">';
        $searchString .= '\n                    <value index=\"vDEF\">' . $form->getUid() . '</value>%';
        $sql = 'select distinct pages.title, pages.uid';
        $sql .= ' from pages left join tt_content on tt_content.pid = pages.uid';
        $sql .= ' where tt_content.list_type = "powermail_pi1"';
        $sql .= ' and tt_content.deleted = 0 and pages.deleted = 0';
        $sql .= ' and tt_content.pi_flexform like "' . $searchString . '"';

        $result = $query->statement($sql)->execute(true);
        return $result;
    }

    /**
     * Find all localized records with
     *        tx_powermail_domain_model_page.forms = "0"
     *
     * @return array
     */
    public function findAllWrongLocalizedPages(): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Page::TABLE_NAME, true);
        return $queryBuilder
            ->select('uid', 'pid', 'title', 'l10n_parent', 'sys_language_uid')
            ->from(Page::TABLE_NAME)
            ->where('(forms = "" or forms = 0) and sys_language_uid > 0 and deleted = 0')
            ->execute()
            ->fetchAll();
    }

    /**
     * Fix wrong localized forms
     *
     * @return void
     */
    public function fixWrongLocalizedPages()
    {
        foreach ($this->findAllWrongLocalizedPages() as $page) {
            $defaultPageUid = $page['l10n_parent'];
            $defaultFormUid = $this->getFormUidFromPageUid($defaultPageUid);
            $localizedFormUid = $this->getLocalizedFormUidFromFormUid($defaultFormUid, $page['sys_language_uid']);
            $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Page::TABLE_NAME);
            $queryBuilder
                ->update(Page::TABLE_NAME)
                ->where('uid = ' . (int)$page['uid'])
                ->set('forms', $localizedFormUid)
                ->execute();
        }
    }

    /**
     * Get all not deleted pages
     *
     * @return int[]
     */
    public function getAllPages()
    {
        $querybuilder = DatabaseUtility::getQueryBuilderForTable('pages', true);
        $rows = $querybuilder->select('uid')->from('pages')->execute()->fetchAll();
        $pids = [];
        foreach ($rows as $row) {
            $pids[] = (int)$row['uid'];
        }
        return $pids;
    }

    /**
     * Get parent form uid form given page uid
     *
     * @param int $pageUid
     * @return int
     */
    protected function getFormUidFromPageUid($pageUid)
    {
        $query = $this->createQuery();
        $sql = 'select forms';
        $sql .= ' from ' . Page::TABLE_NAME;
        $sql .= ' where uid = ' . (int)$pageUid;
        $sql .= ' and deleted = 0';
        $sql .= ' limit 1';
        $row = $query->statement($sql)->execute(true);
        return (int)$row[0]['forms'];
    }

    /**
     * @param int $formUid
     * @param int $sysLanguageUid
     * @return int
     */
    protected function getLocalizedFormUidFromFormUid($formUid, $sysLanguageUid)
    {
        $query = $this->createQuery();
        $sql = 'select uid';
        $sql .= ' from ' . Form::TABLE_NAME;
        $sql .= ' where l10n_parent = ' . (int)$formUid;
        $sql .= ' and sys_language_uid = ' . (int)$sysLanguageUid;
        $sql .= ' and deleted = 0';
        $row = $query->statement($sql)->execute(true);
        return (int)$row[0]['uid'];
    }
}
