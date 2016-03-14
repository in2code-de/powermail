<?php
namespace In2code\Powermail\Hook;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class FlexFormManipulationHook
 * @package In2code\Powermail\Hook
 */
class FlexFormManipulationHook
{

    /**
     * @var array
     */
    protected $row = [];

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var array
     */
    protected $allowedSheets = [
        'main',
        'receiver',
        'sender',
        'thx'
    ];

    /**
     * Add new FlexForm Field at the end of a sheet
     *
     *  Example page TSConfig:
     *      tx_powermail.flexForm.addField {
     *           settings\.flexform\.main\.test._sheet = main
     *           settings\.flexform\.main\.test.label = LLL:EXT:ext/path/locallang_db.xlf:flexform.main.form
     *           settings\.flexform\.main\.test.config.type = input
     *           settings\.flexform\.main\.test.config.eval = trim
     *      }
     *
     *  Will be available in Templates with {settings.main.test}
     *
     * @param array $dataStructArray FlexForm DataStructure
     * @param array $configuration
     * @param array $row table properties
     * @param string $table tableName
     * @param string $fieldName fieldName
     */
    public function getFlexFormDS_postProcessDS(&$dataStructArray, $configuration, $row, $table, $fieldName)
    {
        $this->row = $row;
        $this->table = $table;
        if ($this->isPowermailFlexForm()) {
            foreach ($this->getFieldConfiguration() as $key => $fieldConfiguration) {
                $sheet = $this->getSheetNameAndRemoveFromConfiguration($fieldConfiguration);
                $dataStructArray['sheets'][$sheet]['ROOT']['el'][$key]['TCEforms'] = $fieldConfiguration;
            }
        }
    }

    /**
     * Get field configuration from page TSconfig
     *
     * @return array
     */
    protected function getFieldConfiguration()
    {
        $tsConfiguration = BackendUtility::getPagesTSconfig((int) $this->row['pid']);
        if (!empty($tsConfiguration['tx_powermail.']['flexForm.']['addField.'])) {
            $eConfiguration = $tsConfiguration['tx_powermail.']['flexForm.']['addField.'];
            /** @var TypoScriptService $tsService */
            $tsService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
            $configuration = $tsService->convertTypoScriptArrayToPlainArray($eConfiguration);
            return $configuration;
        }
        return [];
    }

    /**
     * Get sheetname and remove from configuration array
     *
     *
     * @param array $configuration
     * @return string
     */
    protected function getSheetNameAndRemoveFromConfiguration(array &$configuration)
    {
        $sheet = $this->allowedSheets[0];
        if (!empty($configuration['_sheet']) && in_array($configuration['_sheet'], $this->allowedSheets)) {
            $sheet = $configuration['_sheet'];
        }
        unset($configuration['_sheet']);
        return $sheet;
    }

    /**
     * Check if this flexform is loaded in powermail Pi1 context
     *
     * @return bool
     */
    protected function isPowermailFlexForm()
    {
        return $this->table === 'tt_content' && $this->row['CType'] === 'list'
            && $this->row['list_type'] === 'powermail_pi1';
    }
}
