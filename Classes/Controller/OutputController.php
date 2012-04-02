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
 * Controller for powermail frontend output (former part of the powermail_frontend extension)
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Powermail_Controller_OutputController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * mailsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_MailsRepository
	 */
	protected $mailsRepository;

	/**
	 * formsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FormsRepository
	 */
	protected $formsRepository;

	/**
	 * piVars
	 *
	 * @var array
	 */
	protected $piVars;

	/**
	  * Show mails in a list
	  *
	  * @return void
	  */
	public function listAction() {
		// get all mails
		$mails = $this->mailsRepository->findListBySettings($this->settings, $this->piVars);
		$this->view->assign('mails', $mails);

		// get field array for output
		$fields = t3lib_div::trimExplode(',', $this->settings['list']['fields'], 1);
		if (!$fields) {
			$fields = $this->div->getFieldsFromForm($this->settings['main']['form']);
		}
		$this->view->assign('fields', $fields);

		// get piVars
		$this->view->assign('piVars', $this->piVars);
		$this->view->assign('abc', $this->div->getAbcArray());
	}

	/**
	  * Show mails in a list
	  *
	  * @param		Tx_Powermail_Domain_Model_Mails $mail
	  * @return 	void
	  */
	public function showAction(Tx_Powermail_Domain_Model_Mails $mail) {
		$this->view->assign('mail', $mail);

		// get field array for output
		$fields = t3lib_div::trimExplode(',', $this->settings['detail']['fields'], 1);
		if (!$fields) {
			$fields = $this->div->getFieldsFromForm($this->settings['main']['form']);
		}
		$this->view->assign('fields', $fields);
	}

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	protected function initializeAction() {
		$this->div = t3lib_div::makeInstance('Tx_Powermail_Utility_Div');
		$this->div->mergeTypoScript2FlexForm($this->settings, 'Pi2'); // merge typoscript to flexform
		$this->piVars = $this->request->getArguments();

		// check if ts is included
		if (!isset($this->settings['staticTemplate'])) {
			$this->flashMessageContainer->add(Tx_Extbase_Utility_Localization::translate('error_no_typoscript_pi2', 'powermail'));
		}
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

	/**
	 * injectFormsRepository
	 *
	 * @param Tx_Powermail_Domain_Repository_FormsRepository $formsRepository
	 * @return void
	 */
	public function injectFormsRepository(Tx_Powermail_Domain_Repository_FormsRepository $formsRepository) {
		$this->formsRepository = $formsRepository;
	}
}

?>