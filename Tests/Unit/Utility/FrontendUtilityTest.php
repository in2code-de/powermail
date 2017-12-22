<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class FrontendUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\FrontendUtility
 */
class FrontendUtilityTest extends UnitTestCase
{

    /**
     * @var array
     */
    protected $testFilesToDelete = [];

    /**
     * @return void
     * @test
     * @covers ::getStoragePage
     */
    public function getStoragePageReturnsInt()
    {
        $result = FrontendUtility::getStoragePage(123);
        $this->assertSame(123, $result);
    }

    /**
     * Data Provider for getDomainFromUriReturnsString()
     *
     * @return array
     */
    public function getDomainFromUriReturnsStringDataProvider()
    {
        return [
            [
                'http://subdomain.domain.org/folder/file.html',
                'subdomain.domain.org'
            ],
            [
                'ftp://domain.org',
                'domain.org'
            ],
            [
                'https://www.domain.co.uk/',
                'www.domain.co.uk'
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
        $this->assertSame($expectedResult, FrontendUtility::getDomainFromUri($value));
    }

    /**
     * Data Provider for getCountryFromIpReturnsString()
     *
     * @return array
     */
    public function getCountryFromIpReturnsStringDataProvider()
    {
        return [
            [
                '217.72.208.133',
                'Germany'
            ],
            [
                '27.121.255.4',
                'Japan'
            ],
            [
                '5.226.31.255',
                'Spain'
            ],
            [
                '66.85.131.18',
                'United States'
            ],
            [
                '182.118.23.7',
                'China'
            ],
        ];
    }

    /**
     * @param string $ip
     * @param string $expectedResult
     * @dataProvider getCountryFromIpReturnsStringDataProvider
     * @return void
     * @test
     * @covers ::getCountryFromIp
     */
    public function getCountryFromIpReturnsString($ip, $expectedResult)
    {
        $this->assertSame($expectedResult, FrontendUtility::getCountryFromIp($ip));
    }

    /**
     * Dataprovider getSubFolderOfCurrentUrlReturnsString()
     *
     * @return array
     */
    public function getSubFolderOfCurrentUrlReturnsStringDataProvider()
    {
        return [
            [
                true,
                true,
                'http://www.in2code.de',
                'http://www.in2code.de/',
                '/'
            ],
            [
                false,
                true,
                'http://www.in2code.de',
                'http://www.in2code.de/',
                '/'
            ],
            [
                true,
                false,
                'http://www.in2code.de',
                'http://www.in2code.de/',
                '/'
            ],
            [
                false,
                false,
                'http://www.in2code.de',
                'http://www.in2code.de/',
                ''
            ],
            [
                true,
                true,
                'http://www.in2code.de',
                'http://www.in2code.de/subfolder/',
                '/subfolder/'
            ],
            [
                false,
                true,
                'http://www.in2code.de',
                'http://www.in2code.de/subfolder/',
                'subfolder/'
            ],
            [
                true,
                false,
                'http://www.in2code.de',
                'http://www.in2code.de/subfolder/',
                '/subfolder'
            ],
            [
                false,
                false,
                'http://www.in2code.de',
                'http://www.in2code.de/subfolder/',
                'subfolder'
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
        $this->assertSame($expectedResult, $result);
    }
}
