<?php

namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Tests\Helper\TestingHelper;
use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class FrontendUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\FrontendUtility
 */
class FrontendUtilityTest extends UnitTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        TestingHelper::setDefaultConstants();
    }

    /**
     * @return void
     * @test
     * @covers ::getStoragePage
     */
    public function getStoragePageReturnsInt()
    {
        self::assertSame(123, FrontendUtility::getStoragePage(123));
        self::assertNotSame(1, FrontendUtility::getStoragePage());
    }

    /**
     * @return void
     * @test
     * @covers ::getCurrentPageIdentifier
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTyposcriptFrontendController
     */
    public function getCurrentPageIdentifierReturnsInt()
    {
        $result = FrontendUtility::getCurrentPageIdentifier();
        self::assertSame(0, $result);
    }

    /**
     * @return void
     * @test
     * @covers ::getSysLanguageUid
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTyposcriptFrontendController
     */
    public function getSysLanguageUidReturnsInt()
    {
        self::assertSame(0, FrontendUtility::getSysLanguageUid());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @covers ::getPluginName
     */
    public function testGetPluginNameReturnsString()
    {
        self::assertSame('tx_powermail_pi1', FrontendUtility::getPluginName());

        $_GET['tx_powermail_pi2']['action'] = 'test';
        self::assertSame('tx_powermail_pi2', FrontendUtility::getPluginName());

        unset($_GET['tx_powermail_pi2']);
        $_GET['tx_powermail_web_powermailm1']['action'] = 'test';
        self::assertSame('tx_powermail_web_powermailm1', FrontendUtility::getPluginName());
        unset($_GET);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @covers ::getActionName
     */
    public function testGetActionName()
    {
        $_GET['tx_powermail_pi1']['action'] = '';
        self::assertSame('', FrontendUtility::getActionName());
        $_GET['tx_powermail_pi1']['action'] = 'test';
        self::assertSame('test', FrontendUtility::getActionName());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isLoggedInFrontendUser
     */
    public function isLoggedInFrontendUserReturnsBool()
    {
        self::assertFalse(FrontendUtility::isLoggedInFrontendUser());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::getPropertyFromLoggedInFrontendUser
     */
    public function getPropertyFromLoggedInFrontendUserReturnsString()
    {
        self::assertSame('', FrontendUtility::getPropertyFromLoggedInFrontendUser('uid'));
        self::assertSame('', FrontendUtility::getPropertyFromLoggedInFrontendUser('foobar'));
    }

    /**
     * Data Provider for getDomainFromUriReturnsString()
     *
     * @return array
     */
    public static function getDomainFromUriReturnsStringDataProvider(): array
    {
        return [
            [
                'http://subdomain.domain.org/folder/file.html',
                'subdomain.domain.org',
            ],
            [
                'ftp://domain.org',
                'domain.org',
            ],
            [
                'https://www.domain.co.uk/',
                'www.domain.co.uk',
            ],
        ];
    }

    /**
     * @param string $value
     * @param string $expectedResult
     * @dataProvider getDomainFromUriReturnsStringDataProvider
     * @return void
     * @test
     * @covers ::getDomainFromUri
     */
    public function getDomainFromUriReturnsString($value, $expectedResult)
    {
        self::assertSame($expectedResult, FrontendUtility::getDomainFromUri($value));
    }

    /**
     * Data Provider for getCountryFromIpReturnsString()
     *
     * @return array
     */
    public static function getCountryFromIpReturnsStringDataProvider(): array
    {
        return [
            [
                '217.72.208.133',
                'Germany',
            ],
            [
                '27.121.255.4',
                'Japan',
            ],
            [
                '5.226.31.255',
                'Spain',
            ],
            [
                '66.85.131.18',
                'United States',
            ],
            [
                '182.118.23.7',
                'China',
            ],
        ];
    }

    /**
     * @param string $ipAddress
     * @param string $expectedResult
     * @dataProvider getCountryFromIpReturnsStringDataProvider
     * @return void
     * @test
     * @covers ::getCountryFromIp
     */
    public function getCountryFromIpReturnsString($ipAddress, $expectedResult)
    {
        self::assertSame($expectedResult, FrontendUtility::getCountryFromIp($ipAddress));
    }

    /**
     * Dataprovider getSubFolderOfCurrentUrlReturnsString()
     *
     * @return array
     */
    public static function getSubFolderOfCurrentUrlReturnsStringDataProvider(): array
    {
        return [
            [
                true,
                true,
                'http://www.in2code.de',
                'http://www.in2code.de/',
                '/',
            ],
            [
                false,
                true,
                'http://www.in2code.de',
                'http://www.in2code.de/',
                '/',
            ],
            [
                true,
                false,
                'http://www.in2code.de',
                'http://www.in2code.de/',
                '/',
            ],
            [
                false,
                false,
                'http://www.in2code.de',
                'http://www.in2code.de/',
                '',
            ],
            [
                true,
                true,
                'http://www.in2code.de',
                'http://www.in2code.de/subfolder/',
                '/subfolder/',
            ],
            [
                false,
                true,
                'http://www.in2code.de',
                'http://www.in2code.de/subfolder/',
                'subfolder/',
            ],
            [
                true,
                false,
                'http://www.in2code.de',
                'http://www.in2code.de/subfolder/',
                '/subfolder',
            ],
            [
                false,
                false,
                'http://www.in2code.de',
                'http://www.in2code.de/subfolder/',
                'subfolder',
            ],
        ];
    }

    /**
     * @param bool $leadingSlash will be prepended
     * @param bool $trailingSlash will be appended
     * @param string $host
     * @param string $url
     * @param string $expectedResult
     * @return void
     * @dataProvider getSubFolderOfCurrentUrlReturnsStringDataProvider
     * @test
     * @covers ::getSubFolderOfCurrentUrl
     */
    public function getSubFolderOfCurrentUrlReturnsString($leadingSlash, $trailingSlash, $host, $url, $expectedResult)
    {
        $result = FrontendUtility::getSubFolderOfCurrentUrl($leadingSlash, $trailingSlash, $host, $url);
        self::assertSame($expectedResult, $result);
    }
}
