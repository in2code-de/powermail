<?php

declare(strict_types=1);
namespace In2code\Powermail\Tca;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\DatabaseUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\ArrayUtility as CoreArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

/**
 * Class ShowFormNoteEditForm
 * to display chosen form and some more information in the FlexForm of an opened powermail content element
 */
class ShowFormNoteEditForm extends AbstractFormElement
{
    /**
     * @var array
     */
    protected $formProperties = [];

    /**
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
     * @throws DBALException
     * @throws DeprecatedException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidConfigurationTypeException
     * @throws RouteNotFoundException
     */
    public function render(): array
    {
        $result = $this->initializeResultArray();
        $result['html'] = $this->getHtml();
        return $result;
    }

    /**
     * @throws DBALException
     * @throws DeprecatedException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidConfigurationTypeException
     * @throws RouteNotFoundException
     */
    protected function getHtml(): string
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

    protected function getLabels(): array
    {
        return [
            'formname' => $this->getLabel('formnote.formname'),
            'storedinpage' => $this->getLabel('formnote.storedinpage'),
            'pages' => $this->getLabel('formnote.pages'),
            'fields' => $this->getLabel('formnote.fields'),
            'noform' => $this->getLabel('formnote.noform'),
            'new' => $this->getLabel('formnote.new'),
            'edit' => $this->getLabel('formnote.edit'),
        ];
    }

    /**
     * Get form uid of a localized form (only if needed)
     */
    protected function getLocalizedFormUid(int $uid, int $sysLanguageUid): int
    {
        if ($sysLanguageUid > 0) {
            $row = BackendUtilityCore::getRecordLocalization(Form::TABLE_NAME, $uid, $sysLanguageUid);
            if (!empty($row['uid'])) {
                $uid = (int)$row['uid'];
            }
        }

        return $uid;
    }

    /**
     * Get localized label
     */
    protected function getLabel(string $key): string
    {
        $languageService = ObjectUtility::getLanguageService();
        return htmlspecialchars($languageService->sL($this->locallangPath . 'flexform.main.' . $key));
    }

    /**
     * Build URI for edit link
     *
     * @throws RouteNotFoundException
     */
    protected function getEditFormLink(): string
    {
        if (!CoreArrayUtility::isValidPath($this->getFormProperties(), 'uid')) {
            return '';
        }

        return BackendUtility::createEditUri(Form::TABLE_NAME, (int)$this->getFormProperties()['uid']);
    }

    /**
     * Build URI for new link
     *
     * @throws DeprecatedException
     * @throws RouteNotFoundException
     */
    protected function getNewFormLink(): string
    {
        return BackendUtility::createNewUri(Form::TABLE_NAME, $this->getPageIdentifierForNewForms());
    }

    /**
     * Add possibility to set the pid for new forms with page TSConfig:
     *      tx_powermail.flexForm.newFormPid = 123
     * If empty, the current pid will be taken
     *
     * @throws DeprecatedException
     */
    protected function getPageIdentifierForNewForms(): int
    {
        $pageIdentifier = $this->getPageIdentifierFromExistingContentElements((int)$this->data['databaseRow']['pid']);
        $tsConfiguration = BackendUtility::getPagesTSconfig($pageIdentifier);
        if (!empty($tsConfiguration['tx_powermail.']['flexForm.']['newFormPid'])) {
            return (int)$tsConfiguration['tx_powermail.']['flexForm.']['newFormPid'];
        }

        return $pageIdentifier;
    }

    /**
     * If there is already an existing content element in the same column, $params[row][pid] is filled with
     * (tt_content.uid * -1). This information helps to find the correct pageIdentifier.
     */
    protected function getPageIdentifierFromExistingContentElements(int $pageIdentifier): int
    {
        if ($pageIdentifier < 0) {
            $parentRec = BackendUtilityCore::getRecord('tt_content', abs($pageIdentifier), 'pid');
            $pageIdentifier = (int)$parentRec['pid'];
        }

        return $pageIdentifier;
    }

    protected function getFormProperties(): array
    {
        if (empty($this->formProperties)) {
            $row = BackendUtilityCore::getRecord(Form::TABLE_NAME, $this->getRelatedFormUid());
            if ($row !== null && $row !== []) {
                $this->formProperties = $row;
            }
        }

        return $this->formProperties;
    }

    /**
     * Get related form
     */
    protected function getRelatedFormUid(): int
    {
        $flexFormArray = (array)$this->data['databaseRow']['pi_flexform']['data']['main']['lDEF'] ?? [];
        $formUid = (int)($flexFormArray['settings.flexform.main.form']['vDEF'][0] ?? 0);
        $language = (int)($this->data['databaseRow']['sys_language_uid'][0]
            ?? $this->data['databaseRow']['sys_language_uid'] ?? 0);
        return $this->getLocalizedFormUid($formUid, $language);
    }

    /**
     * pages.* form page where current form is stored
     */
    protected function getStoragePageProperties(): array
    {
        if (!CoreArrayUtility::isValidPath($this->getFormProperties(), 'pid')) {
            return [];
        }

        return (array)BackendUtilityCore::getRecord(
            'pages',
            (int)$this->getFormProperties()['pid'],
            '*',
            '',
            false
        );
    }

    /**
     * Get array with related page titles to a form
     *      ["page1", "page2"]
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws DBALException
     */
    protected function getRelatedPages(): array
    {
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            return $this->getRelatedPagesAlternative();
        }

        if (!CoreArrayUtility::isValidPath($this->getFormProperties(), 'uid')) {
            return [];
        }

        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('p.title')
            ->from(Form::TABLE_NAME, 'fo')
            ->join('fo', Page::TABLE_NAME, 'p', 'p.form = fo.uid')
            ->where('fo.uid = ' . (int)$this->getFormProperties()['uid'] . ' and p.deleted = 0')
            ->setMaxResults(1000)
            ->executeQuery()
            ->fetchAllAssociative();
        return ArrayUtility::flatten($rows, 'title');
    }

    /**
     * Get array with related pages to a form
     * if replaceIrreWithElementBrowser is active
     *
     * @throws DBALException
     */
    protected function getRelatedPagesAlternative(): array
    {
        $pageTitlesReduced = [];
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME);
        $pageUids = $queryBuilder
            ->select('pages')
            ->from(Form::TABLE_NAME)
            ->where('uid = ' . (int)$this->getFormProperties()['uid'])
            ->executeQuery()
            ->fetchAllAssociative();
        if (!empty($pageUids[0]['pages'])) {
            $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Page::TABLE_NAME);
            $pageTitles = $queryBuilder
                ->select('title')
                ->from(Page::TABLE_NAME)
                ->where('uid in (' . StringUtility::integerList($pageUids[0]['pages']) . ')')
                ->executeQuery()
                ->fetchAllAssociative();

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
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws DBALException
     */
    protected function getRelatedFields(): array
    {
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            return $this->getRelatedFieldsAlternative();
        }

        if (!CoreArrayUtility::isValidPath($this->getFormProperties(), 'uid')) {
            return [];
        }

        $titles = [];
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('f.title')
            ->from(Form::TABLE_NAME, 'fo')
            ->join('fo', Page::TABLE_NAME, 'p', 'p.form = fo.uid')
            ->join('p', Field::TABLE_NAME, 'f', 'f.page = p.uid')
            ->where('fo.uid = ' . (int)$this->getFormProperties()['uid'] . ' and p.deleted = 0 and f.deleted = 0')
            ->setMaxResults(1000)
            ->executeQuery()
            ->fetchAllAssociative();
        foreach ($rows as $row) {
            $titles[] = $row['title'];
        }

        return $titles;
    }

    /**
     * Get array with related fields to a form
     * if replaceIrreWithElementBrowser is active
     *
     * @throws DBALException
     */
    protected function getRelatedFieldsAlternative(): array
    {
        $fieldTitlesReduced = [];
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME);
        $pageUids = $queryBuilder
            ->select('pages')
            ->from(Form::TABLE_NAME)
            ->where('uid = ' . (int)$this->getFormProperties()['uid'])
            ->executeQuery()
            ->fetchAllAssociative();
        if (!empty($pageUids[0]['pages'])) {
            $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Page::TABLE_NAME, true);
            $pageUids = $queryBuilder
                ->select('uid')
                ->from(Page::TABLE_NAME)
                ->where('uid in (' . StringUtility::integerList($pageUids[0]['pages']) . ') and deleted=0')
                ->executeQuery()
                ->fetchAllAssociative();
            foreach ($pageUids as $uidRow) {
                $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Field::TABLE_NAME);
                $rows = $queryBuilder
                    ->select('title')
                    ->from(Field::TABLE_NAME)
                    ->where('page = ' . (int)$uidRow['uid'])
                    ->executeQuery()
                    ->fetchAllAssociative();
                foreach ($rows as $row) {
                    $fieldTitlesReduced[] = $row['title'];
                }
            }
        }

        return $fieldTitlesReduced;
    }
}
