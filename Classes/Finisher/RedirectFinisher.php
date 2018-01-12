<?php
declare(strict_types=1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Service\RedirectUriService;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

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
     */
    public function redirectToUriFinisher()
    {
        /** @var RedirectUriService $redirectService */
        $redirectService = ObjectUtility::getObjectManager()->get(RedirectUriService::class, $this->contentObject);
        $uri = $redirectService->getRedirectUri();
        if (!empty($uri) && $this->isRedirectEnabled()) {
            HttpUtility::redirect($uri);
        }
    }

    /**
     * @return bool
     */
    protected function isRedirectEnabled()
    {
        return !(!empty($this->settings['main']['optin']) && empty($this->arguments['hash']));
    }

    /**
     * Initialize
     */
    public function initializeFinisher()
    {
        $this->arguments = GeneralUtility::_GP('tx_powermail_pi1');
    }
}
