<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class UniqueValidator
 * @noinspection PhpUnused
 */
class UniqueValidator extends AbstractValidator
{
    /**
     * @param Mail $mail
     * @return bool
     * @throws Exception
     */
    public function isValid($mail)
    {
        if (!empty($this->settings['validation']['unique'])) {
            foreach ($this->settings['validation']['unique'] as $marker => $amount) {
                if ($amount > 0) {
                    foreach ($mail->getAnswers() as $answer) {
                        /** @var Answer $answer */
                        if ($answer->getField()->getMarker() === $marker) {
                            /** @var MailRepository $mailRepository */
                            $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
                            $numberOfMails = $mailRepository->findByMarkerValueForm(
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
            }
        }
        return $this->isValidState();
    }

    /**
     * Get storage pid from FlexForm, TypoScript or current page
     *
     * @return int
     */
    protected function getStoragePid(): int
    {
        $pid = (int)$this->settings['main']['pid'];
        if (!empty($this->flexForm['settings']['flexform']['main']['pid'])) {
            $pid = (int)$this->flexForm['settings']['flexform']['main']['pid'];
        }
        if ($pid === 0) {
            $pid = FrontendUtility::getCurrentPageIdentifier();
        }
        return $pid;
    }
}
