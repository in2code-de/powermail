<?php
namespace In2code\Powermail\Domain\Validator;

use \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Thorsten Boock <thorsten@nerdcenter.de>
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
 * AbstractValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 *
	 * @inject
	 */
	protected $objectManager;

	/**
	 * formRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\FormRepository
	 * @inject
	 */
	protected $formRepository;

	/**
	 * @var \In2code\Powermail\Utility\Div
	 *
	 * @inject
	 */
	protected $div;

	/**
	 * SignalSlot Dispatcher
	 *
	 * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher;

	/**
	 * Return variable
	 *
	 * @var bool
	 */
	protected $isValid = TRUE;

	/**
	 * TypoScript Setup for powermail Pi1
	 */
	protected $settings;

	/**
	 * @param boolean $isValid
	 * @return void
	 */
	public function setIsValid($isValid) {
		$this->isValid = $isValid;
	}

	/**
	 * @return boolean
	 */
	public function getIsValid() {
		return $this->isValid;
	}

	/**
	 * Set Error
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param string $label
	 * @return void
	 */
	protected function setErrorAndMessage(\In2code\Powermail\Domain\Model\Field $field, $label) {
		$this->setIsValid(FALSE);
		$this->addError($label, $field->getMarker());
	}

	/**
	 * Check if javascript validation is activated
	 *
	 * @return bool
	 */
	protected function isServerValidationEnabled() {
		return $this->settings['validation.']['server'] === '1';
	}

	/**
	 * @param ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectTypoScript(ConfigurationManagerInterface $configurationManager) {
		$typoScriptSetup = $configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
		);
		$this->settings = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
	}

}
