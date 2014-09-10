<?php
namespace In2code\Powermail\Domain\Validator;

/**
 * CustomValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * GNU Lesser General Public License, version 3 or later
 */
class CustomValidator extends \In2code\Powermail\Domain\Validator\StringValidator {

	/**
	 * Custom validation of given Params
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return bool
	 */
	public function isValid($mail) {
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__, array($mail, $this));

		return $this->getIsValid();
	}

}