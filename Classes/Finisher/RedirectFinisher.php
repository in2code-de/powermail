<?php
declare(strict_types = 1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Service\RedirectUriService;
use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class RedirectFinisher
 */
class RedirectFinisher extends AbstractFinisher implements FinisherInterface
{
    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * Redirect user after form submit
     *
     * @return void
     * @throws Exception
     */
    public function redirectToUriFinisher(): void
    {
        $redirectService = GeneralUtility::makeInstance(RedirectUriService::class, $this->contentObject);
        $uri = $redirectService->getRedirectUri();
        if (!empty($uri) && $this->isRedirectEnabled()) {
            HttpUtility::redirect($uri);
        }
    }

    /**
     * @return bool
     */
    protected function isRedirectEnabled(): bool
    {
        return !(!empty($this->settings['main']['optin']) && empty($this->arguments['hash']));
    }

    /**
     * Initialize
     */
    public function initializeFinisher(): void
    {
        $this->arguments = FrontendUtility::getArguments();
    }
}
