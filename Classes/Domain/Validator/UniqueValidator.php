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
 *          GNU Lesser General Public License, version 3 or later
 */
class UniqueValidator extends AbstractValidator
{

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
    public function isValid($mail)
    {
        if (empty($this->settings['validation.']['unique.'])) {
            return $this->isValidState();
        }
        foreach ($this->settings['validation.']['unique.'] as $marker => $amount) {
            if ((int) $amount === 0) {
                continue;
            }
            foreach ($mail->getAnswers() as $answer) {
                /** @var Answer $answer */
                if ($answer->getField()->getMarker() === $marker) {
                    $numberOfMails = $this->mailRepository->findByMarkerValueForm(
                        $marker,
                        $answer->getValue(),
                        $mail->getForm(),
                        FrontendUtility::getStoragePage($this->getStoragePid())
                    )->count();

                    if ($amount <= $numberOfMails) {
                        $this->setErrorAndMessage($answer->getField(), 'unique');
                    }
                }
            }
        }

        return $this->isValidState();
    }

    /**
     * Get storage pid from FlexForm, TypoScript or current page
     *
     * @return int
     */
    protected function getStoragePid()
    {
        $pid = (int) $this->settings['main.']['pid'];
        if (!empty($this->flexForm['main']['lDEF']['settings.flexform.main.pid']['vDEF'])) {
            $pid = (int) $this->flexForm['main']['lDEF']['settings.flexform.main.pid']['vDEF'];
        }
        if ($pid === 0) {
            $pid = (int) FrontendUtility::getCurrentPageIdentifier();
        }
        return $pid;
    }
}
