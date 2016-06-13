<?php
namespace In2code\Powermail\Tca;

use In2code\Powermail\Domain\Model\Form;
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
 * Powermail Form Selector - used in FlexForm
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 *
 */
class FormSelectorUserFunc
{

    /**
     * Create Array for Form Selection
     *
     *      Show all forms only from a pid and it's subpages:
     *          tx_powermail.flexForm.formSelection = 123
     *
     *      Show all forms only from this pid and it's subpages:
     *          tx_powermail.flexForm.formSelection = current
     *
     *      Commaseparated values are allowed. If no TSConfig set, all forms will be shown
     *
     * @param array $params
     * @return void
     */
    public function getForms(&$params)
    {
        $params['items'] = [];
        foreach ($this->getStartPids() as $startPid) {
            foreach ($this->getAllForms($startPid, $params['row']['sys_language_uid']) as $form) {
                $params['items'][] = [
                    htmlspecialchars($form['title']),
                    (int)$form['uid']
                ];
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
        $tsConfiguration = BackendUtility::getPagesTSconfig(BackendUtility::getPidFromBackendPage());
        $startPids = [];
        if (!empty($tsConfiguration['tx_powermail.']['flexForm.']['formSelection'])) {
            $startPidList =
                GeneralUtility::trimExplode(',', $tsConfiguration['tx_powermail.']['flexForm.']['formSelection'], true);
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
        $select = 'fo.uid, fo.title';
        $from = Form::TABLE_NAME . ' fo';
        $where = 'fo.deleted = 0 and fo.hidden = 0 and ' .
            '(fo.sys_language_uid IN (-1,0) or ' .
            '(fo.l10n_parent = 0 and fo.sys_language_uid = ' . (int)$language . '))';
        if (!empty($startPid)) {
            $where .= ' and fo.pid in (' . $this->getPidListFromStartingPoint($startPid) . ')';
        }
        $groupBy = '';
        $orderBy = 'fo.title ASC';
        $limit = 10000;
        $res = ObjectUtility::getDatabaseConnection()->exec_SELECTquery(
            $select,
            $from,
            $where,
            $groupBy,
            $orderBy,
            $limit
        );

        $array = [];
        if ($res) {
            while (($row = ObjectUtility::getDatabaseConnection()->sql_fetch_assoc($res))) {
                $array[] = $row;
            }
        }

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
        /** @var QueryGenerator $queryGenerator */
        $queryGenerator = ObjectUtility::getObjectManager()->get(QueryGenerator::class);
        $list = $queryGenerator->getTreeList($startPid, 10, 0, 1);
        return $list;
    }
}
