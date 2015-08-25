<?php
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\FrontendUtility;

/**
 * UniqueValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class UniqueValidator extends AbstractValidator {

	/**
	 * @var \In2code\Powermail\Domain\Repository\MailRepository
	 * @inject
	 */
	protected $mailRepository;

	/**
	 * Validation of given Params
	 *
	 * @param Mail $mail
	 * @return bool
	 */
	public function isValid($mail) {
		if (empty($this->settings['validation.']['unique.'])) {
			return $this->isValidState();
		}
		foreach ($this->settings['validation.']['unique.'] as $marker => $amount) {
			if (intval($amount) === 0) {
				continue;
			}
			foreach ($mail->getAnswers() as $answer) {
				/** @var Answer $answer */
				if ($answer->getField()->getMarker() === $marker) {
					if (
						$amount <= $this->mailRepository->findByMarkerValueForm(
							$marker,
							$answer->getValue(),
							$mail->getForm(),
							FrontendUtility::getStoragePage($this->settings['main']['pid'])
						)->count()
					) {
						$this->setErrorAndMessage($answer->getField(), 'unique');
					}
				}
			}
		}

		return $this->isValidState();
	}
}