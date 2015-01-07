<?php
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Utility\Div;

/**
 * UniqueValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class UniqueValidator extends AbstractValidator {

	/**
	 * mailRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\MailRepository
	 * @inject
	 */
	protected $mailRepository;

	/**
	 * Validation of given Params
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return bool
	 */
	public function isValid($mail) {
		if (empty($this->settings['validation.']['unique.'])) {
			return $this->getIsValid();
		}
		foreach ($this->settings['validation.']['unique.'] as $marker => $amount) {
			if (intval($amount) === 0) {
				continue;
			}
			foreach ($mail->getAnswers() as $answer) {
				/** @var \In2code\Powermail\Domain\Model\Answer $answer */
				if ($answer->getField()->getMarker() === $marker) {
					if (
						$amount <= $this->mailRepository->findByMarkerValueForm(
							$marker,
							$answer->getValue(),
							$mail->getForm(),
							Div::getStoragePage($this->settings['main']['pid'])
						)->count()
					) {
						$this->setErrorAndMessage($answer->getField(), 'unique');
					}
				}
			}
		}

		return $this->getIsValid();
	}
}