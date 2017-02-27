<?php
namespace In2code\Powermail\Domain\Repository;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extensionmanager\Utility\DatabaseUtility;

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
 * FieldRepository
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
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
     * Fix wrong localized fields with markers
     *
     * @return void
     */
    public function fixFilledMarkersInLocalizedFields()
    {
        $this->getDatabaseConnection()->exec_UPDATEquery(
            Field::TABLE_NAME,
            'sys_language_uid > 0 and deleted = 0 and marker != ""',
            ['marker' => '']
        );
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
        $select = 'uid,pid,title,l10n_parent,sys_language_uid';
        $from = Field::TABLE_NAME;
        $where = '(pages = "" or pages = 0) and sys_language_uid > 0 and deleted = 0';
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
    public function fixWrongLocalizedFields()
    {
        foreach ($this->findAllWrongLocalizedFields() as $field) {
            $defaultFieldUid = $field['l10n_parent'];
            $defaultPageUid = $this->getPageUidFromFieldUid($defaultFieldUid);
            $localizedPageUid = $this->getLocalizedPageUidFromPageUid($defaultPageUid, $field['sys_language_uid']);
            $this->getDatabaseConnection()->exec_UPDATEquery(
                Field::TABLE_NAME,
                'uid = ' . (int)$field['uid'],
                ['pages' => $localizedPageUid]
            );
        }
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
        $form = $this->formRepository->findByUid($formUid);
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
    public function getMarkerFromUid($uid)
    {
        $marker = '';
        $row = (array)ObjectUtility::getDatabaseConnection()->exec_SELECTgetSingleRow(
            'marker',
            Field::TABLE_NAME,
            'uid=' . (int)$uid
        );
        if (!empty($row['marker'])) {
            $marker = $row['marker'];
        }
        return $marker;
    }
}
