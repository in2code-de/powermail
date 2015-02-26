<?php
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Utility\BasicFileFunctions;

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
 * Class for uploading files and check if they are valid
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class UploadValidator extends AbstractValidator {

	/**
	 * Validation of given Mail Params
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return bool
	 */
	public function isValid($mail) {
		foreach ($mail->getAnswers() as $answer) {
			if ($answer->getValueType() === 3) {
				if (!is_array($answer->getValue())) {
					continue;
				}

				foreach ($answer->getValue() as $value) {
					if (!BasicFileFunctions::checkExtension($value, $this->settings['misc.']['file.']['extension'])) {
						$this->setErrorAndMessage($answer->getField(), 'upload_extension');
						continue;
					}
					if (!BasicFileFunctions::checkFilesize($value, $this->settings)) {
						$this->setErrorAndMessage($answer->getField(), 'upload_size');
						continue;
					}
				}
			}
		}

		return $this->getIsValid();
	}
}