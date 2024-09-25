<?php

declare(strict_types=1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Service\RedirectUriService;
use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RedirectFinisher
 */
class RedirectFinisher extends AbstractFinisher implements FinisherInterface
{
    /**
     * @var array
     */
    protected array $arguments = [];

    /**
     * Redirect user after form submit
     *
     * @return void
     * @throws PropagateResponseException
     */
    public function redirectToUriFinisher(): void
    {
        $redirectService = GeneralUtility::makeInstance(RedirectUriService::class, $this->contentObject);
        $uri = $redirectService->getRedirectUri();
        if (!empty($uri) && $this->isRedirectEnabled()) {
            $responseFactory = GeneralUtility::makeInstance(ResponseFactory::class);
            $response = $responseFactory
                ->createResponse(303)
                ->withAddedHeader('location', $uri);
            throw new PropagateResponseException($response);
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
