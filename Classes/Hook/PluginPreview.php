<?php
namespace In2code\Powermail\Hooks;

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

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Service\FlexFormService;

/**
 * Contains a preview rendering for the powermail page module
 */
class PluginPreview implements PageLayoutViewDrawItemHookInterface
{

    /**
     * @var array
     */
    protected $row = [];

    /**
     * @var array
     */
    protected $flexFormData;

    /**
     * @var string
     */
    protected $templatePathAndFile = 'EXT:powermail/Resources/Private/Templates/Hook/PluginPreview.html';

    /**
     * Preprocesses the preview rendering of a content element
     *
     * @param PageLayoutView $parentObject Calling parent object
     * @param bool $drawItem Whether to draw the item using the default functionality
     * @param string $headerContent Header content
     * @param string $itemContent Item content
     * @param array $row Record row of tt_content
     * @return void
     */
    public function preProcess(
        PageLayoutView &$parentObject,
        &$drawItem,
        &$headerContent,
        &$itemContent,
        array &$row
    ) {
        if (!ConfigurationUtility::isDisablePluginInformationActive()) {
            $this->initialize($row);
            if ($this->row['CType'] === 'list') {
                switch ($this->row['list_type']) {
                    case 'powermail_pi1':
                        $drawItem = false;
                        $headerContent = '';
                        $itemContent = $this->getPluginInformation('Pi1');
                        break;
                    case 'powermail_pi2':
                        $drawItem = false;
                        $headerContent = '';
                        $itemContent = $this->getPluginInformation('Pi2');
                        break;
                    default:
                }
            }
        }
    }

    /**
     * @param string @pluginName
     * @return string
     */
    protected function getPluginInformation($pluginName)
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->templatePathAndFile));
        $standaloneView->assignMultiple(
            [
                'row' => $this->row,
                'flexFormData' => $this->flexFormData,
                'formUid' => $this->getLocalizedFormUid($this->getFormUid(), $this->getSysLanguageUid()),
                'receiverEmail' => $this->getReceiverEmail(),
                'receiverEmailDevelopmentContext' => ConfigurationUtility::getDevelopmentContextEmail(),
                'mails' => $this->getLatestMails(),
                'pluginName' => $pluginName,
                'enableMailPreview' => !ConfigurationUtility::isDisablePluginInformationMailPreviewActive(),
                'form' => $this->getFormTitleByUid(
                    ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.main.form')
                )
            ]
        );
        return $standaloneView->render();
    }

    /**
     * Get latest three emails to this form
     *
     * @return QueryResult
     */
    protected function getLatestMails()
    {
        /** @var MailRepository $mailRepository */
        $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
        return $mailRepository->findLatestByForm(
            ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.main.form')
        );
    }

    /**
     * Get receiver mail
     *
     * @return string
     */
    protected function getReceiverEmail()
    {
        $receiver = ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.receiver.email');
        if ((int)ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.receiver.type') === 1) {
            $receiver = 'Frontenduser Group '
                . ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.receiver.fe_group');
        }
        if ((int)ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.receiver.type') === 2) {
            $receiver = 'Predefined "' .
                ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.receiver.predefinedemail') . '"';
        }
        return $receiver;
    }

    /**
     * Get form title from uid
     *
     * @param int $uid Form uid
     * @return string
     */
    protected function getFormTitleByUid($uid)
    {
        $uid = $this->getLocalizedFormUid($uid, $this->getSysLanguageUid());
        $select = 'title';
        $from = Form::TABLE_NAME;
        $where = 'uid=' . (int)$uid;
        $row = ObjectUtility::getDatabaseConnection()->exec_SELECTgetSingleRow($select, $from, $where);
        return $row['title'];
    }

    /**
     * Get form uid of a localized form
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
            $where = 'sys_language_uid=' . (int)$sysLanguageUid . ' and l10n_parent=' . (int)$uid;
            $row = ObjectUtility::getDatabaseConnection()->exec_SELECTgetSingleRow($select, $from, $where);
            if (!empty($row['uid'])) {
                $uid = (int)$row['uid'];
            }
        }
        return $uid;
    }

    /**
     * Get form uid
     *
     * @return int
     */
    protected function getFormUid()
    {
        return (int)$this->flexFormData['settings']['flexform']['main']['form'];
    }

    /**
     * Get current sys_language_uid from page content
     *
     * @return int
     */
    protected function getSysLanguageUid()
    {
        if (!empty($this->row['sys_language_uid'])) {
            return (int)$this->row['sys_language_uid'];
        }
        return 0;
    }

    /**
     * @param array $row
     * @return void
     */
    protected function initialize(array $row)
    {
        $this->row = $row;

        /** @var FlexFormService $flexFormService */
        $flexFormService = ObjectUtility::getObjectManager()->get(FlexFormService::class);
        $this->flexFormData = $flexFormService->convertFlexFormContentToArray($this->row['pi_flexform']);
    }
}
