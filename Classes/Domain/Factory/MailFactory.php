<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Factory;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Repository\UserRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class MailFactory
 */
class MailFactory
{

    /**
     * @param Mail $mail
     * @param array $settings
     * @return void
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @codeCoverageIgnore
     */
    public function prepareMailForPersistence(Mail $mail, array $settings)
    {
        $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
        $marketingInfos = SessionUtility::getMarketingInfos();
        $mail
            ->setPid(FrontendUtility::getStoragePage($settings['main']['pid']))
            ->setSenderMail($mailRepository->getSenderMailFromArguments($mail))
            ->setSenderName($mailRepository->getSenderNameFromArguments($mail))
            ->setSubject($settings['receiver']['subject'])
            ->setReceiverMail($settings['receiver']['email'])
            ->setBody(DebugUtility::viewArray($mailRepository->getVariablesWithMarkersFromMail($mail)))
            ->setSpamFactor(SessionUtility::getSpamFactorFromSession())
            ->setTime((time() - SessionUtility::getFormStartFromSession($mail->getForm()->getUid(), $settings)))
            ->setUserAgent(GeneralUtility::getIndpEnv('HTTP_USER_AGENT'))
            ->setMarketingRefererDomain($marketingInfos['refererDomain'])
            ->setMarketingReferer($marketingInfos['referer'])
            ->setMarketingCountry($marketingInfos['country'])
            ->setMarketingMobileDevice($marketingInfos['mobileDevice'])
            ->setMarketingFrontendLanguage($marketingInfos['frontendLanguage'])
            ->setMarketingBrowserLanguage($marketingInfos['browserLanguage'])
            ->setMarketingPageFunnel($marketingInfos['pageFunnel']);
        $this->setFeuser($mail);
        $this->setSenderIp($mail);
        $this->setHidden($mail, $settings);
        $this->setAnswersPid($mail, $settings);
    }

    /**
     * @param Mail $mail
     * @return void
     */
    protected function setFeuser(Mail $mail)
    {
        if (FrontendUtility::isLoggedInFrontendUser()) {
            $userRepository = ObjectUtility::getObjectManager()->get(UserRepository::class);
            $mail->setFeuser($userRepository->findByUid(FrontendUtility::getPropertyFromLoggedInFrontendUser('uid')));
        }
    }

    /**
     * @param Mail $mail
     * @return void
     */
    protected function setSenderIp(Mail $mail)
    {
        if (!ConfigurationUtility::isDisableIpLogActive()) {
            $mail->setSenderIp(GeneralUtility::getIndpEnv('REMOTE_ADDR'));
        }
    }

    /**
     * @param Mail $mail
     * @param array $settings
     * @return void
     */
    protected function setHidden(Mail $mail, array $settings)
    {
        if ($settings['main']['optin'] || $settings['db']['hidden']) {
            $mail->setHidden(true);
        }
    }

    /**
     * @param Mail $mail
     * @param array $settings
     * @return void
     */
    protected function setAnswersPid(Mail $mail, array $settings)
    {
        foreach ($mail->getAnswers() as $answer) {
            $answer->setPid(FrontendUtility::getStoragePage($settings['main']['pid']));
        }
    }
}
