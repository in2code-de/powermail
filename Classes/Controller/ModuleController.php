<?php

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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Powermail_Controller_ModuleController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * mailsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_MailsRepository
	 */
	protected $mailsRepository;

	/**
	 * piVars
	 *
	 * @var array
	 */
	protected $piVars;

	/**
	 * div
	 *
	 * @var object
	 */
	protected $div;

	/**
	 * List View Backend
	 *
	 * @return void
	 */
	public function listBeAction() {
		$mails = $this->mailsRepository->findAllInPid(t3lib_div::_GP('id'), $this->settings, $this->piVars);
		$firstMail = $this->mailsRepository->findFirstInPid(t3lib_div::_GP('id'));

		$this->view->assign('mails', $mails);
		$this->view->assign('firstMail', $firstMail);
		$this->view->assign('piVars', $this->piVars);
		$this->view->assign('pid', t3lib_div::_GP('id'));
		$this->view->assign('token', t3lib_BEfunc::getUrlToken('tceAction'));
		$this->view->assign('perPage', ($this->settings['perPage'] ? $this->settings['perPage'] : 10));
	}

	/**
	 * Check View Backend
	 *
	 * @param string $email email address
	 * @return void
	 */
	public function checkBeAction($email = NULL) {
		$this->view->assign('pid', t3lib_div::_GP('id'));

		if ($email) {
			if (t3lib_div::validEmail($email)) {
				$body = 'New <b>Test Email</b> from User ' . $GLOBALS['BE_USER']->user['username'] . ' (' . t3lib_div::getIndpEnv('HTTP_HOST') . ')';

				$message = t3lib_div::makeInstance('t3lib_mail_Message');
				$message
					->setTo(array($email => 'Receiver'))
					->setFrom(array('powermail@domain.net' => 'powermail'))
					->setSubject('New Powermail Test Email')
					->setBody($body, 'text/html')
					->send();

				$this->view->assign('issent', $message->isSent());
				$this->view->assign('email', $email);
			}
		}
	}

	/**
	 * Reporting
	 *
	 * @param string $subaction could be 'form' or 'marketing'
	 * @return void
	 */
	public function reportingBeAction($subaction = NULL) {
		if ($subaction == 'form') {
			$this->forward('reportingFormBe');
		}
		if ($subaction == 'marketing') {
			$this->forward('reportingMarketingBe');
		}
	}

	/**
	 * Reporting Form
	 *
	 * @return void
	 */
	public function reportingFormBeAction() {
		$mails = $this->mailsRepository->findAllInPid(t3lib_div::_GP('id'), $this->settings, $this->piVars);
		$firstMail = $this->mailsRepository->findFirstInPid(t3lib_div::_GP('id'));
		$groupedAnswers = Tx_Powermail_Utility_Div::getGroupedMailAnswers($mails);

		$this->view->assign('groupedAnswers', $groupedAnswers);
		$this->view->assign('mails', $mails);
		$this->view->assign('firstMail', $firstMail);
		$this->view->assign('piVars', $this->piVars);
		$this->view->assign('pid', t3lib_div::_GP('id'));
		$this->view->assign('token', t3lib_BEfunc::getUrlToken('tceAction'));
		$this->view->assign('perPage', ($this->settings['perPage'] ? $this->settings['perPage'] : 10));
	}

	/**
	 * Reporting Marketing
	 *
	 * @return void
	 */
	public function reportingMarketingBeAction() {
		$mails = $this->mailsRepository->findAllInPid(t3lib_div::_GP('id'), $this->settings, $this->piVars);
		$firstMail = $this->mailsRepository->findFirstInPid(t3lib_div::_GP('id'));
		$groupedMarketingStuff = Tx_Powermail_Utility_Div::getGroupedMarketingStuff($mails);

		$this->view->assign('groupedMarketingStuff', $groupedMarketingStuff);
		$this->view->assign('mails', $mails);
		$this->view->assign('firstMail', $firstMail);
		$this->view->assign('piVars', $this->piVars);
		$this->view->assign('pid', t3lib_div::_GP('id'));
		$this->view->assign('token', t3lib_BEfunc::getUrlToken('tceAction'));
		$this->view->assign('perPage', ($this->settings['perPage'] ? $this->settings['perPage'] : 10));
	}

	/**
	 * Export Action
	 *
	 * @param array $export export settings
	 * @return void
	 */
	public function exportBeAction(array $export = array()) {
		if ($export['format'] == 'xls') {
			$this->forward('exportXlsBe', NULL, NULL, array('export' => $export));
		}
		$this->forward('exportCsvBe', NULL, NULL, array('export' => $export));
	}

	/**
	 * Export Action for XLS Files
	 *
	 * @param array $export export settings
	 * @return void
	 */
	public function exportXlsBeAction(array $export = array()) {
		$mails = $this->mailsRepository->findByUidList($export['mails'], $export['sorting']);
		$this->view->assign('mails', $mails);
		$this->view->assign('fields', t3lib_div::trimExplode(',', $export['fields'], 1));

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: inline; filename="' . ($this->settings['export']['filenameXls'] ? $this->settings['export']['filenameXls'] : 'export.xls') . '"');
		header('Pragma: no-cache');
	}

	/**
	 * Export Action for CSV Files
	 *
	 * @param array $export export settings
	 * @return void
	 */
	public function exportCsvBeAction(array $export = array()) {
		$mails = $this->mailsRepository->findByUidList($export['mails'], $export['sorting']);
		$this->view->assign('mails', $mails);
		$this->view->assign('fields', t3lib_div::trimExplode(',', $export['fields'], 1));

		header('Content-Type: text/x-csv');
		header('Content-Disposition: attachment; filename="' . ($this->settings['export']['filenameCsv'] ? $this->settings['export']['filenameCsv'] : 'export.csv') . '"');
		header('Pragma: no-cache');
	}

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	protected function initializeAction() {
		$this->div = t3lib_div::makeInstance('Tx_Powermail_Utility_Div');
		$this->piVars = $this->request->getArguments();
	}

	/**
	 * injectMailsRepository
	 *
	 * @param Tx_Powermail_Domain_Repository_MailsRepository $mailsRepository
	 * @return void
	 */
	public function injectMailsRepository(Tx_Powermail_Domain_Repository_MailsRepository $mailsRepository) {
		$this->mailsRepository = $mailsRepository;
	}
}

?>