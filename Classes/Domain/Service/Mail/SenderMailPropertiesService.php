<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class SenderMailPropertiesService to get email array for sender attributes
 * for sender emails
 */
class SenderMailPropertiesService
{
    use SignalTrait;

    /**
     * TypoScript settings as plain array
     *
     * @var array
     */
    protected $settings = [];

    /**
     * TypoScript configuration for cObject parsing
     *
     * @var array
     */
    protected $configuration = [];

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
        /** @var TypoScriptService $typoScriptService */
        $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
        $this->configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
    }

    /**
     * Get sender email from form settings. If empty, take default from TypoScript or TYPO3 configuration
     *
     * @return string
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function getSenderEmail()
    {
        if ($this->settings['sender']['email'] !== '') {
            $senderEmail = $this->settings['sender']['email'];
        } else {
            $senderEmail = ConfigurationUtility::getDefaultMailFromAddress();
            TypoScriptUtility::overwriteValueFromTypoScript(
                $senderEmail,
                $this->configuration['sender.']['default.'],
                'senderEmail'
            );
        }

        $signalArguments = [&$senderEmail, $this];
        $this->signalDispatch(__CLASS__, __FUNCTION__, $signalArguments);
        return $senderEmail;
    }

    /**
     * Get sender name from form settings. If empty, take default from TypoScript or TYPO3 configuration.
     *
     * @return string
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function getSenderName()
    {
        if ($this->settings['sender']['name'] !== '') {
            $senderName = $this->settings['sender']['name'];
        } else {
            $senderName = ConfigurationUtility::getDefaultMailFromName();
            TypoScriptUtility::overwriteValueFromTypoScript(
                $senderName,
                $this->configuration['sender.']['default.'],
                'senderName'
            );
        }

        $signalArguments = [&$senderName, $this];
        $this->signalDispatch(__CLASS__, __FUNCTION__, $signalArguments);
        return $senderName;
    }
}
