<?php
namespace In2code\Powermail\Tca;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\PageRepository;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class FormSelectorUserFunc
 */
class FormSelectorUserFunc
{

    /**
     * @var null|PageRepository
     */
    protected $pageRepository = null;

    /**
     * PageTS of current page
     *
     * @var array
     */
    protected $tsConfiguration = [];

    /**
     * FormSelectorUserFunc constructor.
     */
    public function __construct()
    {
        $this->pageRepository = ObjectUtility::getObjectManager()->get(PageRepository::class);
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
     */
    public function getForms(array &$params)
    {
        $params['items'] = [];
        foreach ($this->getStartPids() as $startPid) {
            foreach ($this->getAllForms($startPid, $params['flexParentDatabaseRow']['sys_language_uid']) as $form) {
                if ($this->hasUserAccessToPage((int)$form['pid'])) {
                    $params['items'][] = [
                        htmlspecialchars($form['title']),
                        (int)$form['uid']
                    ];
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
    protected function getStartPids()
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
     */
    protected function getAllForms($startPid, $language)
    {
        $select = 'uid, title, pid';
        $from = Form::TABLE_NAME;
        $where = 'deleted=0 and hidden=0 and ' .
            '(sys_language_uid IN (-1,0) or ' .
            '(l10n_parent = 0 and sys_language_uid = ' . (int)$language . '))';
        if (!empty($startPid)) {
            $where .= ' and pid in (' . $this->getPidListFromStartingPoint($startPid) . ')';
        }
        $groupBy = '';
        $orderBy = 'title ASC';
        $limit = 10000;
        $array = ObjectUtility::getDatabaseConnection()->exec_SELECTgetRows(
            $select,
            $from,
            $where,
            $groupBy,
            $orderBy,
            $limit
        );

        return $array;
    }

    /**
     * Get commaseparated list of PID under a starting Page
     *
     * @param int $startPid
     * @return string
     */
    protected function getPidListFromStartingPoint($startPid = 0)
    {
        $queryGenerator = ObjectUtility::getObjectManager()->get(QueryGenerator::class);
        return $queryGenerator->getTreeList($startPid, 10, 0, 1);
    }

    /**
     * Check if backend user has access to given page
     *
     * @param int $pageIdentifier
     * @return bool
     */
    protected function hasUserAccessToPage($pageIdentifier)
    {
        if (!$this->hasFullAccess()) {
            $properties = $this->pageRepository->getPropertiesFromUid($pageIdentifier);
            return BackendUtility::getBackendUserAuthentication()->doesUserHaveAccess($properties, 1);
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
    protected function hasFullAccess()
    {
        return BackendUtility::isBackendAdmin() ||
        (!empty($this->tsConfiguration['tx_powermail.']['flexForm.']['formSelection']) &&
            $this->tsConfiguration['tx_powermail.']['flexForm.']['formSelection'] === '*');
    }
}
