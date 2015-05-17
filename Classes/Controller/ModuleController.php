<?php
namespace In2code\Powermail\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use In2code\Powermail\Utility\Div;
use In2code\Powermail\Utility\Reporting;

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
 * Controller for powermail list views (BE and FE)
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class ModuleController extends AbstractController {

	/**
	 * @param string $forwardToAction
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @return void
	 */
	public function dispatchAction($forwardToAction = 'list') {
		$this->forward($forwardToAction);
	}

	/**
	 * List View Backend
	 *
	 * @return void
	 */
	public function listAction() {
		$formUids = $this->mailRepository->findGroupedFormUidsToGivenPageUid($this->id);
		$firstFormUid = Div::conditionalVariable($this->piVars['filter']['form'], key($formUids));
		$this->view->assignMultiple(
			array(
				'mails' => $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars),
				'formUids' => $formUids,
				'firstForm' => $this->formRepository->findByUid($firstFormUid),
				'piVars' => $this->piVars,
				'pid' => $this->id,
				'token' => BackendUtility::getUrlToken('tceAction'),
				'perPage' => ($this->settings['perPage'] ? $this->settings['perPage'] : 10)
			)
		);
	}

	/**
	 * Export Action for XLS Files
	 *
	 * @return void
	 */
	public function exportXlsAction() {
		$this->view->assignMultiple(
			array(
				'mails' => $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars),
				'fieldUids' => GeneralUtility::trimExplode(
					',',
					Div::conditionalVariable($this->piVars['export']['fields'], ''),
					TRUE
				)
			)
		);

		$fileName = Div::conditionalVariable($this->settings['export']['filenameXls'], 'export.xls');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: inline; filename="' . $fileName . '"');
		header('Pragma: no-cache');
	}

	/**
	 * Export Action for CSV Files
	 *
	 * @return void
	 */
	public function exportCsvAction() {
		$this->view->assignMultiple(
			array(
				'mails' => $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars),
				'fieldUids' => GeneralUtility::trimExplode(
					',',
					Div::conditionalVariable($this->piVars['export']['fields'], ''),
					TRUE
				)
			)
		);

		$fileName = Div::conditionalVariable($this->settings['export']['filenameCsv'], 'export.csv');
		header('Content-Type: text/x-csv');
		header('Content-Disposition: attachment; filename="' . $fileName . '"');
		header('Pragma: no-cache');
	}

	/**
	 * Reporting
	 *
	 * @param string $subaction could be 'form' or 'marketing'
	 * @return void
	 */
	public function reportingBeAction($subaction = NULL) {
		switch ($subaction) {
			case 'marketing':
				$this->forward('reportingMarketingBe');
				break;

			case 'form':
				$this->forward('reportingMarketingBe');
				break;

			default:
		}
	}

	/**
	 * Reporting Form
	 *
	 * @return void
	 */
	public function reportingFormBeAction() {
		$mails = $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars);
		$firstMail = $this->mailRepository->findFirstInPid($this->id);
		$groupedAnswers = Reporting::getGroupedAnswersFromMails($mails);

		$this->view->assignMultiple(
			array(
				'groupedAnswers' => $groupedAnswers,
				'mails' => $mails,
				'firstMail' => $firstMail,
				'piVars' => $this->piVars,
				'pid' => $this->id,
				'token' => BackendUtility::getUrlToken('tceAction'),
				'perPage' => ($this->settings['perPage'] ? $this->settings['perPage'] : 10)
			)
		);
	}

	/**
	 * Reporting Marketing
	 *
	 * @return void
	 */
	public function reportingMarketingBeAction() {
		$mails = $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars);
		$firstMail = $this->mailRepository->findFirstInPid($this->id);
		$groupedMarketingStuff = Reporting::getGroupedMarketingPropertiesFromMails($mails);

		$this->view->assignMultiple(
			array(
				'groupedMarketingStuff' => $groupedMarketingStuff,
				'mails' => $mails,
				'firstMail' => $firstMail,
				'piVars' => $this->piVars,
				'pid' => $this->id,
				'token' => BackendUtility::getUrlToken('tceAction'),
				'perPage' => ($this->settings['perPage'] ? $this->settings['perPage'] : 10)
			)
		);
	}

	/**
	 * Tools overview
	 *
	 * @return void
	 */
	public function toolsBeAction() {
	}

	/**
	 * Form Overview
	 *
	 * @return void
	 */
	public function overviewBeAction() {
		$forms = $this->formRepository->findAllInPid($this->id);
		$this->view->assign('forms', $forms);
		$this->view->assign('pid', $this->id);
	}

	/**
	 * Check Permissions
	 *
	 * @return void
	 */
	public function initializeCheckBeAction() {
		$this->checkAdminPermissions();
	}

	/**
	 * Check View Backend
	 *
	 * @param string $email email address
	 * @return void
	 */
	public function checkBeAction($email = NULL) {
		$this->view->assign('pid', $this->id);

		if ($email) {
			if (GeneralUtility::validEmail($email)) {
				$body = 'New <b>Test Email</b> from User ';
				$body .= $GLOBALS['BE_USER']->user['username'] . ' (' . GeneralUtility::getIndpEnv('HTTP_HOST') . ')';

				$senderEmail = 'powermail@domain.net';
				if (GeneralUtility::validEmail($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'])) {
					$senderEmail = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
				}
				$senderName = 'powermail';
				if (!empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'])) {
					$senderName = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
				}
				$message = GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');
				$message
					->setTo(array($email => 'Receiver'))
					->setFrom(array($senderEmail => $senderName))
					->setSubject('New Powermail Test Email')
					->setBody($body, 'text/html')
					->send();

				$this->view->assign('issent', $message->isSent());
				$this->view->assign('email', $email);
			}
		}
	}

	/**
	 * Check Permissions
	 *
	 * @return void
	 */
	public function initializeConverterBeAction() {
		$this->checkAdminPermissions();
	}

	/**
	 * Convert all old forms preflight
	 *
	 * @return void
	 */
	public function converterBeAction() {
		$oldForms = $this->formRepository->findAllOldForms();
		$this->view->assign('oldForms', $oldForms);
	}

	/**
	 * Check Permissions
	 *
	 * @return void
	 */
	public function initializeConverterUpdateBeAction() {
		$this->checkAdminPermissions();
	}

	/**
	 * Convert all old forms
	 *
	 * @param array $converter
	 * @return void
	 */
	public function converterUpdateBeAction($converter) {
		$oldForms = $this->formRepository->findAllOldForms();
		$formCounter = 0;
		$oldFormsWithFieldsetsAndFields = array();
		foreach ($oldForms as $form) {
			$oldFormsWithFieldsetsAndFields[$formCounter] = $form;
			$oldFormsWithFieldsetsAndFields[$formCounter]['_fieldsets'] =
				$this->formRepository->findOldFieldsetsAndFieldsToTtContentRecord($form['uid']);
			$formCounter++;
		}
		/** @var \In2code\Powermail\Utility\FormConverter $formConverter */
		$formConverter = $this->objectManager->get('In2code\Powermail\Utility\FormConverter');
		$result = $formConverter->createNewFromOldForms($oldFormsWithFieldsetsAndFields, $converter);
		$this->view->assign('result', $result);
		$this->view->assign('converter', $converter);
	}

	/**
	 * Check Permissions
	 *
	 * @return void
	 */
	public function initializeFixUploadFolderAction() {
		$this->checkAdminPermissions();
	}

	/**
	 * Create an upload folder
	 *
	 * @return void
	 */
	public function fixUploadFolderAction() {
		GeneralUtility::mkdir(
			GeneralUtility::getFileAbsFileName('uploads/tx_powermail/')
		);
		$this->redirect('checkBe');
	}

	/**
	 * Check Permissions
	 *
	 * @return void
	 */
	public function initializeFixWrongLocalizedFormsAction() {
		$this->checkAdminPermissions();
	}

	/**
	 * Fix wrong localized forms
	 *
	 * @return void
	 */
	public function fixWrongLocalizedFormsAction() {
		$this->formRepository->fixWrongLocalizedForms();
		$this->redirect('checkBe');
	}

	/**
	 * Check Permissions
	 *
	 * @return void
	 */
	public function initializeFixWrongLocalizedPagesAction() {
		$this->checkAdminPermissions();
	}

	/**
	 * Fix wrong localized pages
	 *
	 * @return void
	 */
	public function fixWrongLocalizedPagesAction() {
		$this->pageRepository->fixWrongLocalizedPages();
		$this->redirect('checkBe');
	}

	/**
	 * Check Permissions
	 *
	 * @return void
	 */
	public function initializeFixFilledMarkersInLocalizedFieldsAction() {
		$this->checkAdminPermissions();
	}

	/**
	 * Fix wrong localized markers in fields
	 *
	 * @return void
	 */
	public function fixFilledMarkersInLocalizedFieldsAction() {
		$this->fieldRepository->fixFilledMarkersInLocalizedFields();
		$this->fieldRepository->fixWrongLocalizedFields();
		$this->redirect('checkBe');
	}

	/**
	 * Check if admin is logged in
	 * 		If not, forward to tools overview
	 *
	 * @return void
	 */
	protected function checkAdminPermissions() {
		if (!Div::isBackendAdmin()) {
			$this->controllerContext = $this->buildControllerContext();
			$this->forward('toolsBe');
		}
	}
}