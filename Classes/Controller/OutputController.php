<?php
namespace In2code\Powermail\Controller;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

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
 * Controller for powermail frontend output
 * (former part of the powermail_frontend extension)
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class OutputController extends AbstractController
{

    /**
     * Show mails in a list
     *
     * @return void
     */
    public function listAction()
    {
        $this->prepareFilterPluginVariables($this->piVars, $this->settings['search']['staticPluginsVariables']);
        $fieldArray = $this->getFieldList($this->settings['list']['fields']);
        $searchFields = $this->fieldRepository->findByUids(
            GeneralUtility::trimExplode(',', $this->settings['search']['fields'], true)
        );
        $this->view->assignMultiple(
            [
                'mails' => $this->mailRepository->findListBySettings($this->settings, $this->piVars),
                'searchFields' => $searchFields,
                'fields' => $this->fieldRepository->findByUids($fieldArray),
                'piVars' => $this->piVars,
                'abc' => ArrayUtility::getAbcArray()
            ]
        );
        $this->assignMultipleActions();
    }

    /**
     * Show single mail
     *
     * @param Mail $mail
     * @return void
     */
    public function showAction(Mail $mail)
    {
        $fieldArray = $this->getFieldList($this->settings['single']['fields']);
        $this->view->assignMultiple(
            [
                'fields' => $this->fieldRepository->findByUids($fieldArray),
                'mail' => $mail
            ]
        );
        $this->assignMultipleActions();
    }

    /**
     * Edit mail
     *
     * @param Mail $mail
     * @return void
     */
    public function editAction(Mail $mail = null)
    {
        $fieldArray = $this->getFieldList($this->settings['edit']['fields']);
        $this->view->assignMultiple(
            [
                'selectedFields' => $this->fieldRepository->findByUids($fieldArray),
                'mail' => $mail
            ]
        );
        $this->assignMultipleActions();
    }

    /**
     * Rewrite Arguments to receive a clean mail object
     *
     * @return void
     */
    public function initializeUpdateAction()
    {
        $arguments = $this->request->getArguments();
        if (!FrontendUtility::isAllowedToEdit($this->settings, $arguments['field']['__identity'])) {
            $this->controllerContext = $this->buildControllerContext();
            $this->addFlashmessage(
                LocalizationUtility::translate('PowermailFrontendEditFailed'),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('list');
        }
        $this->reformatParamsForAction();
    }

    /**
     * Update mail
     *
     * @param Mail $mail
     * @validate $mail In2code\Powermail\Domain\Validator\InputValidator
     * @return void
     */
    public function updateAction(Mail $mail)
    {
        $this->uploadService->uploadAllFiles();
        $this->mailRepository->update($mail);
        $this->addFlashmessage(LocalizationUtility::translate('PowermailFrontendEditSuccessful'));
        $this->redirect('edit', null, null, ['mail' => $mail]);
    }

    /**
     * Check authentication
     *
     * @return void
     */
    public function initializeDeleteAction()
    {
        $arguments = $this->request->getArguments();
        if (!FrontendUtility::isAllowedToEdit($this->settings, $arguments['mail'])) {
            $this->controllerContext = $this->buildControllerContext();
            $this->addFlashmessage(
                LocalizationUtility::translate('PowermailFrontendDeleteFailed'),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('list');
        }
    }

    /**
     * Delete mail
     *
     * @param Mail $mail
     * @return void
     */
    public function deleteAction(Mail $mail)
    {
        $this->assignMultipleActions();
        $this->mailRepository->remove($mail);
        $this->addFlashmessage(LocalizationUtility::translate('PowermailFrontendDeleteSuccessful'));
    }

    /**
     * Export mails
     *
     * @param array $export Field Array with mails and format
     * @dontvalidate $export
     * @return void
     */
    public function exportAction($export = [])
    {
        if (!$this->settings['list']['export']) {
            return;
        }
        $mails = $this->mailRepository->findByUidList($export['fields']);

        // get field array for output
        if ($this->settings['list']['fields']) {
            $fieldArray = GeneralUtility::trimExplode(',', $this->settings['list']['fields'], true);
        } else {
            $fieldArray = $this->formRepository->getFieldUidsFromForm($this->settings['main']['form']);
        }
        $fields = $this->fieldRepository->findByUids($fieldArray);

        if ($export['format'] === 'xls') {
            $this->forward('exportXls', null, null, ['mails' => $mails, 'fields' => $fields]);
        }
        $this->forward('exportCsv', null, null, ['mails' => $mails, 'fields' => $fields]);
    }

    /**
     * Export mails XLS
     *
     * @param QueryResult $mails mails objects
     * @param array $fields uid field list
     * @dontvalidate $mails
     * @dontvalidate $fields
     * @return    void
     */
    public function exportXlsAction(QueryResult $mails = null, $fields = [])
    {
        $this->view->assign('mails', $mails);
        $this->view->assign('fields', $fields);
    }

    /**
     * Export mails CSV
     *
     * @param QueryResult $mails mails objects
     * @param array $fields uid field list
     * @dontvalidate $mails
     * @dontvalidate $fields
     * @return void
     */
    public function exportCsvAction(QueryResult $mails = null, $fields = [])
    {
        $this->view->assign('mails', $mails);
        $this->view->assign('fields', $fields);
    }

    /**
     * RSS Action List
     *
     * @return void
     */
    public function rssAction()
    {
        $mails = $this->mailRepository->findListBySettings($this->settings, $this->piVars);
        $this->view->assign('mails', $mails);
        $this->assignMultipleActions();
    }

    /**
     * Object initialization
     *
     * @return void
     */
    public function initializeObject()
    {
        ConfigurationUtility::mergeTypoScript2FlexForm($this->settings, 'Pi2');
    }

    /**
     * Get fieldlist from list or from database
     *
     * @param string $list
     * @return array
     */
    protected function getFieldList($list = '')
    {
        if (!empty($list)) {
            $fieldArray = GeneralUtility::trimExplode(',', $list, true);
        } else {
            $fieldArray = $this->formRepository->getFieldUidsFromForm($this->settings['main']['form']);
        }
        return (array)$fieldArray;
    }

    /**
     * Assign variables
     *
     * @return void
     */
    protected function assignMultipleActions()
    {
        if (empty($this->settings['single']['pid'])) {
            $this->settings['single']['pid'] = FrontendUtility::getCurrentPageIdentifier();
        }
        if (empty($this->settings['list']['pid'])) {
            $this->settings['list']['pid'] = FrontendUtility::getCurrentPageIdentifier();
        }
        if (empty($this->settings['edit']['pid'])) {
            $this->settings['edit']['pid'] = FrontendUtility::getCurrentPageIdentifier();
        }
        $this->view->assign('singlePid', $this->settings['single']['pid']);
        $this->view->assign('listPid', $this->settings['list']['pid']);
        $this->view->assign('editPid', $this->settings['edit']['pid']);
    }

    /**
     * Action initialization
     *
     * @return void
     */
    protected function initializeAction()
    {
        parent::initializeAction();

        // check if ts is included
        if (!isset($this->settings['staticTemplate'])) {
            $this->controllerContext = $this->buildControllerContext();
            $this->addFlashMessage(
                LocalizationUtility::translate('error_no_typoscript_pi2'),
                '',
                AbstractMessage::ERROR
            );
        }
    }

    /**
     * Add parameters to piVars from TypoScript
     *
     * @param array $pluginVariables
     * @param array $parameters
     * @return void
     */
    protected function prepareFilterPluginVariables(&$pluginVariables, $parameters)
    {
        if (!empty($parameters['filter'])) {
            $pluginVariables = (array)$pluginVariables + (array)$parameters;
        }
    }
}
