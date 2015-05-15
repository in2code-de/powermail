<?php
namespace In2code\Powermail\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use In2code\Powermail\Utility\Div;
use In2code\Powermail\Utility\BasicFileFunctions;
use In2code\Powermail\Domain\Model\Mail;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Abstract Controller for powermail
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractController extends ActionController {

	/**
	 * formRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\FormRepository
	 * @inject
	 */
	protected $formRepository;

	/**
	 * pageRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\PageRepository
	 * @inject
	 */
	protected $pageRepository;

	/**
	 * fieldRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * mailRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\MailRepository
	 * @inject
	 */
	protected $mailRepository;

	/**
	 * answerRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\AnswerRepository
	 * @inject
	 */
	protected $answerRepository;

	/**
	 * userRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\UserRepository
	 * @inject
	 */
	protected $userRepository;

	/**
	 * SignalSlot Dispatcher
	 *
	 * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher;

	/**
	 * PersistenceManager
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * Instance for Misc Functions
	 *
	 * @var \In2code\Powermail\Utility\Div
	 * @inject
	 */
	protected $div;

	/**
	 * Content Object
	 *
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $cObj;

	/**
	 * TypoScript configuration
	 *
	 * @var array
	 */
	protected $conf;

	/**
	 * Plugin variables
	 *
	 * @var array
	 */
	protected $piVars;

	/**
	 * message Class
	 *
	 * @var string
	 */
	protected $messageClass = 'error';

	/**
	 * selected page Uid
	 *
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Reformat Array
	 *
	 * @return void
	 */
	public function initializeValidateAjaxAction() {
		$this->reformatParamsForAction();
	}

	/**
	 * Validate field
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return void
	 */
	public function validateAjaxAction(Mail $mail) {
		$pluginVariables = GeneralUtility::_GET('tx_powermail_pi1');

			// get value
		$value = array_shift($pluginVariables['field']);

		$inputValidator = $this->objectManager->get('In2code\Powermail\Domain\Validator\InputValidator');
		$isValid = $inputValidator->isValid($mail, $value);

		$this->view->assign('isValid', $isValid);
		$this->view->assign('errors', $inputValidator->getErrors());
		if (!$isValid) {
			header('HTTP/1.0 404 Not Found');
		}
	}

	/**
	 * Reformat array for createAction
	 *
	 * @return void
	 */
	protected function reformatParamsForAction() {
		$arguments = $this->request->getArguments();
		if (!isset($arguments['field'])) {
			return;
		}
		$newArguments = array(
			'mail' => $arguments['mail']
		);

			// allow subvalues in new property mapper
		$mailMvcArgument = $this->arguments->getArgument('mail');
		$propertyMappingConfiguration = $mailMvcArgument->getPropertyMappingConfiguration();
		$propertyMappingConfiguration->allowProperties('answers');
		$propertyMappingConfiguration->allowCreationForSubProperty('answers');
		$propertyMappingConfiguration->allowModificationForSubProperty('answers');
		$propertyMappingConfiguration->allowProperties('form');
		$propertyMappingConfiguration->allowCreationForSubProperty('form');
		$propertyMappingConfiguration->allowModificationForSubProperty('form');

			// allow creation of new objects (for validation)
		$propertyMappingConfiguration->setTypeConverterOptions(
			'TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\PersistentObjectConverter',
			array(
				PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED => TRUE,
				PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED => TRUE
			)
		);

		$i = 0;
		foreach ((array) $arguments['field'] as $marker => $value) {
				// ignore internal fields (honeypod)
			if (substr($marker, 0, 2) === '__') {
				continue;
			}
			$fieldUid = $this->div->getFieldUidFromMarker($marker, $arguments['mail']['form']);
				// Skip fields without Uid (secondary password, upload)
			if ($fieldUid === 0) {
				continue;
			}

				// allow subvalues in new property mapper
			$propertyMappingConfiguration->forProperty('answers')->allowProperties($i);
			$propertyMappingConfiguration->forProperty('answers.' . $i)->allowAllProperties();
			$propertyMappingConfiguration->allowCreationForSubProperty('answers.' . $i);
			$propertyMappingConfiguration->allowModificationForSubProperty('answers.' . $i);

			$valueType = Div::getDataTypeFromFieldType(
				$this->div->getFieldTypeFromMarker($marker, $arguments['mail']['form'])
			);
			if ($valueType === 3 && is_array($value)) {
				$value = BasicFileFunctions::getUniqueNamesForFileUploads($value, $this->settings, FALSE);
			}
			if (is_array($value)) {
				if (empty($value)) {
					$value = '';
				} else {
					$value = json_encode($value);
				}
			}
			$newArguments['mail']['answers'][$i] = array(
				'field' => strval($fieldUid),
				'value' => $value,
				'valueType' => $valueType
			);

				// edit form: add answer id
			if (!empty($arguments['field']['__identity'])) {
				$newArguments['mail']['answers'][$i]['__identity'] = $this->answerRepository->findByFieldAndMail(
					$fieldUid,
					$arguments['field']['__identity']
				)->getUid();
			}
			$i++;
		}

			// edit form: add mail id
		if (!empty($arguments['field']['__identity'])) {
			$newArguments['mail']['__identity'] = $arguments['field']['__identity'];
		}

		$this->request->setArguments($newArguments);
		$this->request->setArgument('field', NULL);
	}

	/**
	 * Assigns all values, which should be available in all views
	 *
	 * @return void
	 */
	protected function assignForAll() {
		$frontendLanguageUid = $GLOBALS['TSFE']->tmpl->setup['config.']['sys_language_uid'];
		$this->view->assign('languageUid', ($frontendLanguageUid ? $frontendLanguageUid : 0));
		$this->view->assign('Pid', $GLOBALS['TSFE']->id);
	}

	/**
	 * Object initialization
	 *
	 * @return void
	 */
	protected function initializeAction() {
		$this->piVars = $this->request->getArguments();
		$this->id = GeneralUtility::_GP('id');
	}

	/**
	 * Deactivate errormessages in flashmessages
	 *
	 * @return bool
	 */
	protected function getErrorFlashMessage() {
		return FALSE;
	}
}