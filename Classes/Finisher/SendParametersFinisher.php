<?php

declare(strict_types=1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * SendParametersFinisher to send params via CURL
 */
class SendParametersFinisher extends AbstractFinisher implements FinisherInterface
{
    /**
     * @var ConfigurationManagerInterface
     * local instance that can be manipulated via start() and has no influence to parent::contentObject
     */
    protected ConfigurationManagerInterface $configurationManager;

    protected ?ContentObjectRenderer $contentObjectLocal = null;

    /**
     * TypoScript configuration part sendPost
     *
     * Example configuration TypoScript:
     * plugin.tx_powermail.settings.setup.marketing.sendPost {
     *      _enable = TEXT
     *      _enable.value = 1
     *      targetUrl = http://target.com/
     *      values = COA
     *      values {
     *          10 = TEXT
     *          10 {
     *              field = firstname
     *              wrap = &fn=|
     *          }
     *      }
     * }
     */
    protected array $configuration;

    public function __construct(
        Mail $mail,
        array $configuration,
        array $settings,
        bool $formSubmitted,
        string $actionMethodName,
        ContentObjectRenderer $contentObject
    ) {
        parent::__construct($mail, $configuration, $settings, $formSubmitted, $actionMethodName, $contentObject);
        GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        $this->contentObjectLocal = $this->contentObject;
    }

    /**
     * Send values via curl to a third party software
     */
    public function sendFinisher(): void
    {
        if ($this->isEnabled()) {
            $curlSettings = $this->getCurlSettings();
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $curlSettings['url']);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $curlSettings['params']);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            if (!empty($curlSettings['username']) && !empty($curlSettings['password'])) {
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($curl, CURLOPT_USERPWD, $curlSettings['username'] . ':' . $curlSettings['password']);
            }

            curl_exec($curl);
            curl_close($curl);
            $this->writeToDevelopmentLog();
        }
    }

    protected function writeToDevelopmentLog(): void
    {
        if (!empty($this->configuration['debug'])) {
            $logger = ObjectUtility::getLogger(self::class);
            $logger->info('SendPost Values', $this->getCurlSettings());
        }
    }

    protected function getCurlSettings(): array
    {
        return [
            'url' => $this->configuration['targetUrl'],
            'username' => $this->configuration['username'] ?? '',
            'password' => $this->configuration['password'] ?? '',
            'params' => $this->getValues(),
        ];
    }

    /**
     * Get parameters
     */
    protected function getValues(): string
    {
        return $this->contentObjectLocal->cObjGetSingle(
            $this->configuration['values'],
            $this->configuration['values.']
        );
    }

    /**
     * Check if sendPost is activated
     *      - if it's enabled via TypoScript
     *      - if form was final submitted (without optin)
     */
    protected function isEnabled(): bool
    {
        return $this->contentObjectLocal->cObjGetSingle(
            $this->configuration['_enable'],
            $this->configuration['_enable.']
        ) === '1' && $this->isFormSubmitted();
    }

    public function initializeFinisher(): void
    {
        $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        $this->contentObjectLocal->start($mailRepository->getVariablesWithMarkersFromMail($this->mail));
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $configuration = $configurationService->getTypoScriptConfiguration();
        $this->configuration = $configuration['marketing.']['sendPost.'];
    }
}
