<?php
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SendParametersFinisher to send params via CURL
 */
class SendParametersFinisher extends AbstractFinisher implements FinisherInterface
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * @var \In2code\Powermail\Domain\Repository\MailRepository
     * @inject
     */
    protected $mailRepository;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObject = null;

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
     *
     * @var array
     */
    protected $configuration;

    /**
     * Send values via curl to a third party software
     *
     * @return void
     */
    public function sendFinisher()
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

    /**
     * Write devlog entry
     *
     * @return void
     */
    protected function writeToDevelopmentLog()
    {
        if ($this->configuration['debug']) {
            GeneralUtility::devLog('SendPost Values', 'powermail', 0, $this->getCurlSettings());
        }
    }

    /**
     * CURL settings
     *
     * @return array
     * @return void
     */
    protected function getCurlSettings()
    {
        return [
            'url' => $this->configuration['targetUrl'],
            'username' => $this->configuration['username'],
            'password' => $this->configuration['password'],
            'params' => $this->getValues()
        ];
    }

    /**
     * Get parameters
     *
     * @return string
     */
    protected function getValues()
    {
        return $this->contentObject->cObjGetSingle($this->configuration['values'], $this->configuration['values.']);
    }

    /**
     * Check if sendPost is activated
     *      - if it's enabled via TypoScript
     *      - if form was final submitted (without optin)
     *
     * @return bool
     */
    protected function isEnabled()
    {
        return $this->contentObject->cObjGetSingle(
            $this->configuration['_enable'],
            $this->configuration['_enable.']
        ) === '1' && $this->isFormSubmitted();
    }

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeFinisher()
    {
        $this->contentObject = $this->configurationManager->getContentObject();
        $this->contentObject->start($this->mailRepository->getVariablesWithMarkersFromMail($this->mail));
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
        $configuration = $configurationService->getTypoScriptConfiguration();
        $this->configuration = $configuration['marketing.']['sendPost.'];
    }
}
