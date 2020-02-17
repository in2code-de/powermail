<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;

/**
 * Class UniqueValidator
 */
class UniqueValidator extends AbstractValidator
{

    /**
     * Validation of given Params
     *
     * @param Mail $mail
     * @return bool
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     */
    public function isValid($mail)
    {
        if (empty($this->settings['validation']['unique'])) {
            return $this->isValidState();
        }
        foreach ($this->settings['validation']['unique'] as $marker => $amount) {
            if ((int)$amount === 0) {
                continue;
            }
            foreach ($mail->getAnswers() as $answer) {
                /** @var Answer $answer */
                if ($answer->getField()->getMarker() === $marker) {
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
