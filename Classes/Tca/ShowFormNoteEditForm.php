<?php
declare(strict_types=1);
namespace In2code\Powermail\Tca;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\DatabaseUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ShowFormNoteEditForm to display chosen form and some
 * more information in the FlexForm of an opened powermail
 * content element
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
            $row = BackendUtilityCore::getRecordLocalization(Form::TABLE_NAME, (int)$uid, (int)$sysLanguageUid);
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
        return htmlspecialchars($languageService->sL($this->locallangPath . 'flexform.main.' . $key));
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
            $parentRec = BackendUtilityCore::getRecord('tt_content', abs($pageIdentifier), 'pid');
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
            $row = BackendUtilityCore::getRecord(Form::TABLE_NAME, (int)$this->getRelatedFormUid());
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
        $row = BackendUtilityCore::getRecord('pages', (int)$this->getFormProperties()['pid'], '*', '', false);
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
    protected function getRelatedPages(): array
    {
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            return $this->getRelatedPagesAlternative();
        }

        $titles = [];
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('p.title')
            ->from(Form::TABLE_NAME, 'fo')
            ->join('fo', Page::TABLE_NAME, 'p', 'p.forms = fo.uid')
            ->where('fo.uid = ' . (int)$this->getFormProperties()['uid'] . ' and p.deleted = 0')
            ->setMaxResults(1000)
            ->execute()
            ->fetchAll();
        foreach ($rows as $row) {
            $titles[] = $row['title'];
        }
        return $titles;
    }

    /**
     * Get array with related pages to a form
     * if replaceIrreWithElementBrowser is active
     *
     * @return array
     */
    protected function getRelatedPagesAlternative(): array
    {
        $pageTitlesReduced = [];
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME);
        $pageUids = $queryBuilder
            ->select('pages')
            ->from(Form::TABLE_NAME)
            ->where('uid = ' . (int)$this->getFormProperties()['uid'])
            ->execute()
            ->fetchAll();
        if (!empty($pageUids[0]['pages'])) {
            $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Page::TABLE_NAME);
            $pageTitles = $queryBuilder
                ->select('title')
                ->from(Page::TABLE_NAME)
                ->where('uid in (' . StringUtility::integerList($pageUids[0]['pages']) . ') and deleted=0')
                ->execute()
                ->fetchAll();

            foreach ($pageTitles as $titleRow) {
                $pageTitlesReduced[] = $titleRow['title'];
            }
        }
        return $pageTitlesReduced;
    }

    /**
     * Get array with related field titles to a form
     *      ["firstname", "lastname", "email"]
     *
     * @return array
     */
    protected function getRelatedFields(): array
    {
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            return $this->getRelatedFieldsAlternative();
        }

        $titles = [];
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('f.title')
            ->from(Form::TABLE_NAME, 'fo')
            ->join('fo', Page::TABLE_NAME, 'p', 'p.forms = fo.uid')
            ->join('p', Field::TABLE_NAME, 'f', 'f.pages = p.uid')
            ->where('fo.uid = ' . (int)$this->getFormProperties()['uid'] . ' and p.deleted = 0 and f.deleted = 0')
            ->setMaxResults(1000)
            ->execute()
            ->fetchAll();
        foreach ($rows as $row) {
            $titles[] = $row['title'];
        }
        return $titles;
    }

    /**
     * Get array with related fields to a form
     * if replaceIrreWithElementBrowser is active
     *
     * @return array
     */
    protected function getRelatedFieldsAlternative(): array
    {
        $fieldTitlesReduced = [];
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME);
        $pageUids = $queryBuilder
            ->select('pages')
            ->from(Form::TABLE_NAME)
            ->where('uid = ' . (int)$this->getFormProperties()['uid'])
            ->execute()
            ->fetchAll();
        if (!empty($pageUids[0]['pages'])) {
            $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Page::TABLE_NAME, true);
            $pageUids = $queryBuilder
                ->select('uid')
                ->from(Page::TABLE_NAME)
                ->where('uid in (' . StringUtility::integerList($pageUids[0]['pages']) . ') and deleted=0')
                ->execute()
                ->fetchAll();
            foreach ($pageUids as $uidRow) {
                $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Field::TABLE_NAME);
                $rows = $queryBuilder
                    ->select('title')
                    ->from(Field::TABLE_NAME)
                    ->where('pages = ' . (int)$uidRow['uid'])
                    ->execute()
                    ->fetchAll();
                foreach ($rows as $row) {
                    $fieldTitlesReduced[] = $row['title'];
                }
            }
        }
        return $fieldTitlesReduced;
    }
}
