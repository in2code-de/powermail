<?php

declare(strict_types=1);

namespace In2code\Powermail\Tests\Unit\Domain\Service;

use In2code\Powermail\Domain\Service\RequestTokenService;
use In2code\Powermail\Domain\Service\RequestTokenStatus;
use In2code\Powermail\Utility\ConfigurationUtility;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\SecurityAspect;
use TYPO3\CMS\Core\Security\RequestToken;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(RequestTokenService::class)]
#[CoversClass(ConfigurationUtility::class)]
final class RequestTokenServiceTest extends UnitTestCase
{
    private const FORM_UID = 12;

    protected bool $resetSingletonInstances = true;

    private Context $context;

    private RequestTokenService $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = new Context();
        $this->subject = new RequestTokenService($this->context);
    }

    public static function extensionConfigurationDataProvider(): array
    {
        return [
            'turned off' => [['enableRequestToken' => '0', 'enableCaching' => '0'], false],
            'turned on' => [['enableRequestToken' => '1', 'enableCaching' => '0'], true],
            'turned on, but form caching is active' => [['enableRequestToken' => '1', 'enableCaching' => '1'], false],
            'not synchronized after an update' => [['enableCaching' => '0'], false],
        ];
    }

    #[Test]
    #[DataProvider('extensionConfigurationDataProvider')]
    public function isActiveRespectsExtensionConfiguration(array $extensionConfiguration, bool $expected): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['powermail'] = $extensionConfiguration;
        self::assertSame($expected, $this->subject->isActive());
    }

    #[Test]
    public function createReturnsNullIfTurnedOff(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['powermail'] = ['enableRequestToken' => '0'];
        self::assertNull($this->subject->create(self::FORM_UID, 3));
    }

    #[Test]
    public function createReturnsTokenForFormIfTurnedOn(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['powermail'] = ['enableRequestToken' => '1'];
        $requestToken = $this->subject->create(self::FORM_UID, 3);

        self::assertInstanceOf(RequestToken::class, $requestToken);
        self::assertSame('powermail/create', $requestToken->scope);
        self::assertSame(['form' => self::FORM_UID, 'plugin' => 3], $requestToken->params);
    }

    public static function validateDataProvider(): array
    {
        return [
            'no token sent' => [null, RequestTokenStatus::Missing],
            'token could not be verified' => [false, RequestTokenStatus::Invalid],
            'token of another scope' => [
                new RequestToken('core/user-auth/fe', null, ['form' => self::FORM_UID]),
                RequestTokenStatus::WrongScope,
            ],
            'token of another form' => [
                new RequestToken('powermail/create', null, ['form' => 99]),
                RequestTokenStatus::WrongScope,
            ],
            'token without form' => [
                new RequestToken('powermail/create'),
                RequestTokenStatus::WrongScope,
            ],
            'valid token' => [
                new RequestToken('powermail/create', null, ['form' => self::FORM_UID, 'plugin' => 3]),
                RequestTokenStatus::Valid,
            ],
        ];
    }

    #[Test]
    #[DataProvider('validateDataProvider')]
    public function validateReturnsStatus(RequestToken|false|null $receivedToken, RequestTokenStatus $expected): void
    {
        SecurityAspect::provideIn($this->context)->setReceivedRequestToken($receivedToken);
        self::assertSame($expected, $this->subject->validate(self::FORM_UID));
    }

    #[Test]
    public function revokeDoesNothingWithoutReceivedToken(): void
    {
        SecurityAspect::provideIn($this->context)->setReceivedRequestToken(null);
        $this->subject->revoke();
        self::assertSame([], SecurityAspect::provideIn($this->context)->getNoncePool()->getRevocableNames());
    }
}
