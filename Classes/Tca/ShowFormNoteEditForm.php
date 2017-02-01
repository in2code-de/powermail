<?php
namespace In2code\Powermail\Tca;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
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
 * Class ShowFormNoteEditForm to display chosen form and some
 * more information in the FlexForm of an opened powermail
 * content element
 *
 * @package In2code\Powermail\Tca
 */
class ShowFormNoteEditForm
{

    /**
     * @var array
     */
    public $params = [];

    /**
     * @var array
     */
    protected $formProperties = [];

    /**
     * Path to locallang file (with : as postfix)
     *
     * @var string
     */
    protected $locallangPath = 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:';

    /**
     * @var string
     */
    protected $templatePathAndFile = 'EXT:powermail/Resources/Private/Templates/Tca/ShowFormNoteEditForm.html';

    /**
     * Show Note which form was selected
     *
     * @param array $params TCA configuration array
     * @return string
     */
    public function showNote(array $params)
    {
        $this->params = $params;
        return $this->renderMarkup();
    }

    /**
     * @return string
     */
    protected function renderMarkup()
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->templatePathAndFile));
        $standaloneView->assignMultiple(
            [
                'formProperties' => $this->getFormProperties(),
                'labels' => $this->getLabels(),
                'uriEditForm' => $this->getEditFormLink(),
                'uriNewForm' => $this->getNewFormLink(),
                'storagePageProperties' => $this->getStoragePageProperties(),
                'relatedPages' => $this->getRelatedPages(),
                'relatedFields' => $this->getRelatedFields(),
            ]
        );
        return $standaloneView->render();
    }

    /**
     * @return array
     */
    protected function getLabels()
    {
        $labels = [
            'formname' => $this->getLabel('formnote.formname'),
            'storedinpage' => $this->getLabel('formnote.storedinpage'),
            'pages' => $this->getLabel('formnote.pages'),
            'fields' => $this->getLabel('formnote.fields'),
            'noform' => $this->getLabel('formnote.noform'),
            'new' => $this->getLabel('formnote.new'),
            'edit' => $this->getLabel('formnote.edit')
        ];
        return $labels;
    }

    /**
     * Get form uid of a localized form (only if needed)
     *
     * @param int $uid
     * @param int $sysLanguageUid
     * @return int
     */
    protected function getLocalizedFormUid($uid, $sysLanguageUid)
    {
        if ($sysLanguageUid > 0) {
            $select = 'uid';
            $from = Form::TABLE_NAME;
            $where = 'sys_language_uid=' . (int)$sysLanguageUid . ' and l10n_parent=' . (int)$uid . ' and deleted = 0';
            $row = ObjectUtility::getDatabaseConnection()->exec_SELECTgetSingleRow($select, $from, $where);
            if (!empty($row['uid'])) {
                $uid = (int)$row['uid'];
            }
        }
        return $uid;
    }

    /**
     * Get localized label
     *
     * @param string $key
     * @return string
     */
    protected function getLabel($key)
    {
        $languageService = ObjectUtility::getLanguageService();
        return $languageService->sL($this->locallangPath . 'flexform.main.' . $key, true);
    }

    /**
     * Build URI for edit link
     *
     * @return string
     */
    protected function getEditFormLink()
    {
        return BackendUtility::createEditUri(Form::TABLE_NAME, $this->getFormProperties()['uid']);
    }

    /**
     * Build URI for new link
     *
     * @return string
     */
    protected function getNewFormLink()
    {
        return BackendUtility::createNewUri(Form::TABLE_NAME, $this->getPageIdentifierForNewForms());
    }

    /**
     * Add possibility to set the pid for new forms with page TSConfig:
     *      tx_powermail.flexForm.newFormPid = 123
     * If empty, the current pid will be taken
     *
     * @return int
     */
    protected function getPageIdentifierForNewForms()
    {
        $pageIdentifier = $this->getPageIdentifierFromExistingContentElements((int)$this->params['row']['pid']);
        $tsConfiguration = BackendUtility::getPagesTSconfig($pageIdentifier);
        if (!empty($tsConfiguration['tx_powermail.']['flexForm.']['newFormPid'])) {
            $pageIdentifier = (int)$tsConfiguration['tx_powermail.']['flexForm.']['newFormPid'];
        }
        return $pageIdentifier;
    }

    /**
     * If there is already an existing content element in the same column, $params[row][pid] is filled with
     * (tt_content.uid * -1). This information helps to find the correct pageIdentifier.
     *
     * @param int $pageIdentifier
     * @return int
     */
    protected function getPageIdentifierFromExistingContentElements($pageIdentifier)
    {
        if ($pageIdentifier < 0) {
            $parentRec = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord(
                'tt_content',
                abs($pageIdentifier),
                'pid'
            );
            $pageIdentifier = (int)$parentRec['pid'];
        }
        return $pageIdentifier;
    }

    /**
     * @return array
     */
    protected function getFormProperties()
    {
        if (empty($this->formProperties)) {
            $row = ObjectUtility::getDatabaseConnection()->exec_SELECTgetSingleRow(
                '*',
                Form::TABLE_NAME,
                'uid=' . (int)$this->getRelatedFormUid() . ' and deleted = 0'
            );
            if (!empty($row)) {
                $this->formProperties = $row;
            }
        }
        return $this->formProperties;
    }

    /**
     * Get related form
     *
     * @return int
     */
    protected function getRelatedFormUid()
    {
        $flexFormArray = (array)$this->params['row']['pi_flexform']['data']['main']['lDEF'];
        $formUid = (int)$flexFormArray['settings.flexform.main.form']['vDEF'][0];
        $formUid = $this->getLocalizedFormUid($formUid, (int)$this->params['row']['sys_language_uid'][0]);
        return $formUid;
    }

    /**
     * pages.* form page where current form is stored
     *
     * @return array|FALSE|NULL
     */
    protected function getStoragePageProperties()
    {
        $properties = [];
        $row = ObjectUtility::getDatabaseConnection()->exec_SELECTgetSingleRow(
            '*',
            'pages',
            'uid=' . (int)$this->getFormProperties()['pid']
        );
        if (!empty($row)) {
            $properties = $row;
        }
        return $properties;
    }

    /**
     * Get array with related page titles to a form
     *      ["page1", "page2"]
     *
     * @return array
     */
    protected function getRelatedPages()
    {
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            return $this->getRelatedPagesAlternative();
        }
        $result = [];
        $select = 'p.title';
        $from = Form::TABLE_NAME . ' fo LEFT JOIN ' . Page::TABLE_NAME . ' p ON p.forms = fo.uid';
        $where = 'fo.uid = ' . (int)$this->getFormProperties()['uid'] . ' and p.deleted = 0';
        $groupBy = '';
        $limit = 1000;
        $res = ObjectUtility::getDatabaseConnection()->exec_SELECTquery($select, $from, $where, $groupBy, '', $limit);
        if ($res) {
            while (($row = ObjectUtility::getDatabaseConnection()->sql_fetch_assoc($res))) {
                $result[] = $row['title'];
            }
        }
        return $result;
    }

    /**
     * Get array with related pages to a form
     * if replaceIrreWithElementBrowser is active
     *
     * @return array
     */
    protected function getRelatedPagesAlternative()
    {
        $select = 'f.pages';
        $from = Form::TABLE_NAME . ' as f';
        $where = 'f.uid = ' . (int)$this->getFormProperties()['uid'];
        $pageUids = ObjectUtility::getDatabaseConnection()->exec_SELECTgetRows($select, $from, $where);
        $select = 'p.title';
        $from = Page::TABLE_NAME . ' as p';
        $where = 'p.uid in (' . StringUtility::integerList($pageUids[0]['pages']) . ') and p.deleted = 0';
        $pageTitles = ObjectUtility::getDatabaseConnection()->exec_SELECTgetRows($select, $from, $where);
        $pageTitlesReduced = [];
        foreach ($pageTitles as $titleRow) {
            $pageTitlesReduced[] = $titleRow['title'];
        }
        return $pageTitlesReduced;
    }

    /**
     * Get array with related field titles to a form
     *      ["firstname", "lastname", "email"]
     *
     * @return array
     */
    protected function getRelatedFields()
    {
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            return $this->getRelatedFieldsAlternative();
        }
        $result = [];
        $select = 'f.title';
        $from = Form::TABLE_NAME . ' fo ' .
            'LEFT JOIN ' . Page::TABLE_NAME . ' p ON p.forms = fo.uid ' .
            'LEFT JOIN ' . Field::TABLE_NAME . ' f ON f.pages = p.uid';
        $where = 'fo.uid = ' . (int)$this->getFormProperties()['uid'] . ' and p.deleted = 0 and f.deleted = 0';
        $groupBy = '';
        $limit = 1000;
        $res = ObjectUtility::getDatabaseConnection()->exec_SELECTquery($select, $from, $where, $groupBy, '', $limit);
        if ($res) {
            while (($row = ObjectUtility::getDatabaseConnection()->sql_fetch_assoc($res))) {
                $result[] = $row['title'];
            }
        }
        return $result;
    }

    /**
     * Get array with related fields to a form
     * if replaceIrreWithElementBrowser is active
     *
     * @return array
     */
    protected function getRelatedFieldsAlternative()
    {
        $select = 'f.pages';
        $from = Form::TABLE_NAME . ' as f';
        $where = 'f.uid = ' . (int)$this->getFormProperties()['uid'];
        $pageUids = ObjectUtility::getDatabaseConnection()->exec_SELECTgetRows($select, $from, $where);
        $select = 'p.uid';
        $from = Page::TABLE_NAME . ' as p';
        $where = 'p.uid in (' . StringUtility::integerList($pageUids[0]['pages']) . ') and p.deleted = 0';
        $pageUids = ObjectUtility::getDatabaseConnection()->exec_SELECTgetRows($select, $from, $where);
        $fieldTitlesReduced = [];
        foreach ($pageUids as $uidRow) {
            $select = 'field.title';
            $from = Field::TABLE_NAME . ' as field';
            $where = 'field.pages = ' . (int)$uidRow['uid'];
            $fieldTitles = ObjectUtility::getDatabaseConnection()->exec_SELECTgetRows($select, $from, $where);
            foreach ($fieldTitles as $titleRow) {
                $fieldTitlesReduced[] = $titleRow['title'];
            }
        }
        return $fieldTitlesReduced;
    }
}
