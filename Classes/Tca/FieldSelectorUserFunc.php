<?php
namespace In2code\Powermail\Tca;

use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ObjectUtility;
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
 * Powermail Field Selector for Pi2 (powermail_frontend)
 * Used in FlexForm
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class FieldSelectorUserFunc
{

    /**
     * Cretae Array for Field Selector
     *
     * @param array $params
     * @return void
     */
    public function getFieldSelection(&$params)
    {
        /** @var FormRepository $formRepository */
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        $formUid = $this->getFormUidFromTtContentUid((int)$params['row']['uid']);
        if (!$formUid) {
            $params['items'] = [
                [
                    'Please select a form (Main Settings)',
                    ''
                ]
            ];
            return;
        }
        foreach ((array)$formRepository->getFieldsFromFormWithSelectQuery($formUid) as $field) {
            $params['items'][] = [
                $field['title'] . ' {' . $field['marker'] . '}',
                $field['uid']
            ];
        }
    }

    /**
     * Return Form Uid from content element
     *
     * @param int $ttContentUid
     * @return int
     */
    protected function getFormUidFromTtContentUid($ttContentUid)
    {
        $row = ObjectUtility::getDatabaseConnection()->exec_SELECTgetSingleRow(
            'pi_flexform',
            'tt_content',
            'uid=' . (int)$ttContentUid
        );
        $flexform = GeneralUtility::xml2array($row['pi_flexform']);
        if (is_array($flexform) && isset($flexform['data']['main']['lDEF']['settings.flexform.main.form']['vDEF'])) {
            return (int)$flexform['data']['main']['lDEF']['settings.flexform.main.form']['vDEF'];
        }
        return 0;
    }
}
