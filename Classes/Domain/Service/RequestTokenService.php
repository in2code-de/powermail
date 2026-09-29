<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\SecurityAspect;
use TYPO3\CMS\Core\Security\RequestToken;

/**
 * Create, validate and revoke CSRF-like request tokens.
 * The token is rendered via <f:form requestToken="{requestToken}"> and bound to a nonce cookie, which is
 * handled by the core RequestTokenMiddleware.
 */
class RequestTokenService
{
    private const SCOPE = 'powermail/create';

    public function __construct(
        protected Context $context,
    ) {
    }

    public function isActive(): bool
    {
        return ConfigurationUtility::isRequestTokenActive();
    }

    /**
     * Get a new request token for a form, or null if request tokens are turned off
     */
    public function create(int $formUid, int $contentUid): ?RequestToken
    {
        if ($this->isActive() === false) {
            return null;
        }
        return RequestToken::create(self::SCOPE)->withMergedParams(['form' => $formUid, 'plugin' => $contentUid]);
    }

    public function validate(int $formUid): RequestTokenStatus
    {
        $requestToken = $this->getSecurityAspect()->getReceivedRequestToken();
        if ($requestToken === null) {
            return RequestTokenStatus::Missing;
        }
        if ($requestToken === false) {
            return RequestTokenStatus::Invalid;
        }
        if ($requestToken->scope !== self::SCOPE || (int)($requestToken->params['form'] ?? 0) !== $formUid) {
            return RequestTokenStatus::WrongScope;
        }
        return RequestTokenStatus::Valid;
    }

    /**
     * Revoke the nonce of the received request token, so the same token can not be sent twice.
     * The core middleware takes care to remove the nonce cookie.
     */
    public function revoke(): void
    {
        $securityAspect = $this->getSecurityAspect();
        $requestToken = $securityAspect->getReceivedRequestToken();
        if ($requestToken instanceof RequestToken && $requestToken->getSigningSecretIdentifier() !== null) {
            $securityAspect->getSigningSecretResolver()->revokeIdentifier($requestToken->getSigningSecretIdentifier());
        }
    }

    protected function getSecurityAspect(): SecurityAspect
    {
        return SecurityAspect::provideIn($this->context);
    }
}
