<?php

declare(strict_types=1);
namespace In2code\Powermail\Tca;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception;
use In2code\Powermail\Database\QueryGenerator;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\PageRepository;
use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\DatabaseUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FormSelectorUserFunc
 * shows forms in FlexForm (selection can be filtered via Page TSConfig)
 * @noinspection PhpUnused
 */
class FormSelectorUserFunc
{
    /**
     * @var PageRepository|null
     */
    protected ?PageRepository $pageRepository = null;

    /**
     * PageTS of current page
     *
     * @var array
     */
    protected array $tsConfiguration = [];

    /**
     * FormSelectorUserFunc constructor.
     * @throws DeprecatedException
     */
    public function __construct()
    {
        $this->pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $this->tsConfiguration = BackendUtility::getPagesTSconfig(BackendUtility::getPidFromBackendPage());
    }

    /**
     * Create array for a select that lists all forms.
     *  Remove form from this array if user has no access to a page where a form is stored.
     *  Also page TSConfig can filter this selection as described below:
     *
     *      Show all forms only from a pid and it's subpages:
     *          tx_powermail.flexForm.formSelection = 123
     *
     *      Show all forms only from this pid and it's subpages:
     *          tx_powermail.flexForm.formSelection = current
     *
     *      Show all forms even for users that may have not access to a page where the form is stored into:
     *          tx_powermail.flexForm.formSelection = *
     *
     *      Commaseparated values are allowed. If no TSConfig set, all forms will be shown
     *
     * @param array $params
     * @return void
     * @throws DBALException
     * @noinspection PhpUnused
     */
    public function getForms(array &$params): void
    {
        if (ArrayUtility::isValidPath($params, 'flexParentDatabaseRow/sys_language_uid')) {
            $params['items'] = [];
            $language = (int)$params['flexParentDatabaseRow']['sys_language_uid'];
            foreach ($this->getStartPids() as $startPid) {
                foreach ($this->getAllForms((int)$startPid, $language) as $form) {
                    if ($this->hasUserAccessToPage((int)$form['pid'])) {
                        $params['items'][] = [
                            BackendUtilityCore::getRecordTitle(Form::TABLE_NAME, $form),
                            (int)$form['uid'],
                        ];
                    }
                }
            }
        }
    }

    /**
     * Get starting page uids
     *      current pid or given pid from Page TSConfig
     *
     * @return array
     */
    protected function getStartPids(): array
    {
        $startPids = [];
        if (!empty($this->tsConfiguration['tx_powermail.']['flexForm.']['formSelection'])) {
            $startPidList = GeneralUtility::trimExplode(
                ',',
                $this->tsConfiguration['tx_powermail.']['flexForm.']['formSelection'],
                true
            );
            foreach ($startPidList as $startPid) {
                if ($startPid === 'current') {
                    $startPid = BackendUtility::getPidFromBackendPage();
                }
                array_push($startPids, (int)$startPid);
            }
        } else {
            $startPids = [0];
        }
        return $startPids;
    }

    /**
     * Get Forms from Database
     *
     * @param int $startPid
     * @param int $language
     * @return array
     * @throws Exception
     */
    protected function getAllForms(int $startPid, int $language): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME);
        return $queryBuilder
            ->select('*')
            ->from(Form::TABLE_NAME)
            ->where($this->getWhereStatement($startPid, $language))
            ->orderBy('title')
            ->setMaxResults(10000)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @param int $startPid
     * @param int $language
     * @return string
     */
    protected function getWhereStatement(int $startPid, int $language): string
    {
        $where = '(sys_language_uid IN (-1,0) or (l10n_parent = 0 and sys_language_uid = ' . (int)$language . '))';
        if (!empty($startPid)) {
            $where .= ' and pid in (' . $this->getPidListFromStartingPoint($startPid) . ')';
        }
        return $where;
    }

    /**
     * Get commaseparated list of PID under a starting Page
     *
     * @param int $startPid
     * @return string
     * @throws Exception
     */
    protected function getPidListFromStartingPoint(int $startPid = 0): string
    {
        $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
        return (string)$queryGenerator->getTreeList($startPid, 10, 0, 1);
    }

    /**
     * Check if backend user has access to given page
     *
     * @param int $pageIdentifier
     * @return bool
     */
    protected function hasUserAccessToPage(int $pageIdentifier): bool
    {
        if (!$this->hasFullAccess()) {
            $properties = $this->pageRepository->getPropertiesFromUid($pageIdentifier);
            if ($properties !== []) {
                return BackendUtility::getBackendUserAuthentication()->doesUserHaveAccess($properties, 1);
            }
        }
        return true;
    }

    /**
     * Check if the current user has full access to all forms
     *      - if the backend user is an admin OR
     *      - if we grant full access with tx_powermail.flexForm.formSelection = *
     *
     * @return bool
     */
    protected function hasFullAccess(): bool
    {
        return BackendUtility::isBackendAdmin() ||
        (!empty($this->tsConfiguration['tx_powermail.']['flexForm.']['formSelection']) &&
            $this->tsConfiguration['tx_powermail.']['flexForm.']['formSelection'] === '*');
    }
}
