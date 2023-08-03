<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Factory;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Repository\UserRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\SessionUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class MailFactory
 */
class MailFactory
{
    /**
     * @param Mail $mail
     * @param array $settings
     * @return void
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @codeCoverageIgnore
     */
    public function prepareMailForPersistence(Mail $mail, array $settings): void
    {
        $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        $marketingInfos = SessionUtility::getMarketingInfos();
        $mail
            ->setSenderMail($mailRepository->getSenderMailFromArguments($mail))
            ->setSenderName($mailRepository->getSenderNameFromArguments($mail))
            ->setSubject($settings['receiver']['subject'])
            ->setReceiverMail($settings['receiver']['email'])
            ->setBody(DebuggerUtility::var_dump($mailRepository->getVariablesWithMarkersFromMail($mail), '', 8, true, false, true))
            ->setSpamFactor(SessionUtility::getSpamFactorFromSession())
            ->setTime((time() - SessionUtility::getFormStartFromSession($mail->getForm()->getUid(), $settings)))
            ->setUserAgent(GeneralUtility::getIndpEnv('HTTP_USER_AGENT'))
            ->setMarketingRefererDomain($marketingInfos['refererDomain'])
            ->setMarketingReferer($marketingInfos['referer'])
            ->setMarketingCountry($marketingInfos['country'])
            ->setMarketingMobileDevice((bool)$marketingInfos['mobileDevice'])
            ->setMarketingFrontendLanguage($marketingInfos['frontendLanguage'])
            ->setMarketingBrowserLanguage($marketingInfos['browserLanguage'])
            ->setMarketingPageFunnel($marketingInfos['pageFunnel']);
        $mail->setPid(FrontendUtility::getStoragePage((int)$settings['main']['pid']));
        $this->setFeuser($mail);
        $this->setSenderIp($mail);
        $this->setHidden($mail, $settings);
        $this->setAnswersPid($mail, $settings);
    }

    /**
     * @param Mail $mail
     * @return void
     */
    protected function setFeuser(Mail $mail): void
    {
        if (FrontendUtility::isLoggedInFrontendUser()) {
            $userRepository = GeneralUtility::makeInstance(UserRepository::class);
            $feUserUid = FrontendUtility::getPropertyFromLoggedInFrontendUser('uid');
            $user = $userRepository->findByUid($feUserUid);
            if ($user !== null) {
                $mail->setFeuser($user);
            }
        }
    }

    /**
     * @param Mail $mail
     * @return void
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected function setSenderIp(Mail $mail): void
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
    protected function setHidden(Mail $mail, array $settings): void
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
    protected function setAnswersPid(Mail $mail, array $settings): void
    {
        foreach ($mail->getAnswers() as $answer) {
            $answer->setPid(FrontendUtility::getStoragePage((int)$settings['main']['pid']));
        }
    }
}
