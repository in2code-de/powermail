<?php
namespace In2code\Powermail\Domain\Repository;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Utility\ObjectUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 in2code GmbH <info@in2code.de>, in2code.de
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
 * PageRepository
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class PageRepository extends AbstractRepository
{

    /**
     * Get title from table "pages" in TYPO3
     *
     * @param int $uid
     * @return string
     */
    public function getPageNameFromUid($uid)
    {
        $query = $this->createQuery();

        $sql = 'select title';
        $sql .= ' from pages';
        $sql .= ' where uid = ' . (int)$uid;
        $sql .= ' limit 1';

        $result = $query->statement($sql)->execute(true);

        return $result[0]['title'];
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
    public function findAllWrongLocalizedPages()
    {
        $pages = [];
        $select = 'uid,pid,title,l10n_parent,sys_language_uid';
        $from = Page::TABLE_NAME;
        $where = '(forms = "" or forms = 0) and sys_language_uid > 0 and deleted = 0';
        $res = $this->getDatabaseConnection()->exec_SELECTquery($select, $from, $where);
        if ($res) {
            while (($row = $this->getDatabaseConnection()->sql_fetch_assoc($res))) {
                $pages[] = $row;
            }
        }
        return $pages;
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
            $this->getDatabaseConnection()->exec_UPDATEquery(
                Page::TABLE_NAME,
                'uid = ' . (int)$page['uid'],
                ['forms' => $localizedFormUid]
            );
        }
    }

    /**
     * Get all not deleted pages
     *
     * @return int[]
     */
    public function getAllPages()
    {
        $rows = ObjectUtility::getDatabaseConnection()->exec_SELECTgetRows('uid', 'pages', 'deleted = 0');
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
