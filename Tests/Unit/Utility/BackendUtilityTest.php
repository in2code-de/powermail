<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Unit\Tests\Fixtures\Utility\BackendUtilityFixture;
use In2code\Powermail\Utility\BackendUtility;
use TYPO3\CMS\Backend\Routing\Route;
use TYPO3\CMS\Backend\Routing\Router;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BackendUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\BackendUtility
 */
class BackendUtilityTest extends UnitTestCase
{

    /**
     * @var array
     */
    protected $testFilesToDelete = [];

    /**
     * Data Provider for isBackendAdminReturnsBool()
     *
     * @return array
     */
    public function isBackendAdminReturnsBoolDataProvider()
    {
        return [
            [
                1,
                true
            ],
            [
                0,
                false
            ],
            [
                null,
                false
            ]
        ];
    }

    /**
     * @param string $value
     * @param bool $expectedResult
     * @dataProvider isBackendAdminReturnsBoolDataProvider
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isBackendAdmin
     * @covers ::getBackendUserAuthentication
     */
    public function isBackendAdminReturnsBool($value, $expectedResult)
    {
        if (is_int($value)) {
            $GLOBALS['BE_USER']->user['admin'] = $value;
        } else {
            $GLOBALS['BE_USER'] = null;
        }
        $this->assertSame($expectedResult, BackendUtility::isBackendAdmin());
    }

    /**
     * Data Provider for getPropertyFromBackendUserReturnsString()
     *
     * @return array
     */
    public function getPropertyFromBackendUserReturnsStringDataProvider()
    {
        return [
            [
                'admin',
                '1',
            ],
            [
                'warningMax',
                3
            ],
            [
                null,
                ''
            ]
        ];
    }

    /**
     * @param string $property
     * @param mixed $value
     * @dataProvider getPropertyFromBackendUserReturnsStringDataProvider
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::getPropertyFromBackendUser
     * @covers ::getBackendUserAuthentication
     */
    public function getPropertyFromBackendUserReturnsString($property, $value)
    {
        if ($value !== null) {
            $GLOBALS['BE_USER']->user[$property] = $value;
        } else {
            $GLOBALS['BE_USER'] = null;
        }
        $this->assertSame($value, BackendUtility::getPropertyFromBackendUser($property));
    }

    /**
     * @return void
     * @test
     * @covers ::createEditUri
     * @covers ::getModuleUrl
     * @covers ::getReturnUrl
     * @covers ::getModuleName
     */
    public function createEditUriReturnsString()
    {
        $result = '/typo3/index.php?M=record_edit&moduleToken=dummyToken&edit%5Btt_content%5D%5B123%5D=edit' .
            '&returnUrl=%2Ftypo3%2Findex.php%3FM%3Dweb_layout%26moduleToken%3DdummyToken';
        $this->assertSame($result, BackendUtility::createEditUri('tt_content', 123));
    }

    /**
     * @return void
     * @test
     * @covers ::createNewUri
     * @covers ::getModuleUrl
     * @covers ::getReturnUrl
     * @covers ::getModuleName
     */
    public function createNewUriReturnsString()
    {
        $result = '/typo3/index.php?M=record_edit&moduleToken=dummyToken&edit%5Btt_content%5D%5B123%5D=new' .
            '&returnUrl=%2Ftypo3%2Findex.php%3FM%3Dweb_layout%26moduleToken%3DdummyToken';
        $this->assertSame($result, BackendUtility::createNewUri('tt_content', 123));
    }

    /**
     * Data Provider for getModuleNameReturnsString()
     *
     * @return array
     */
    public function getModuleNameReturnsStringDataProvider()
    {
        return [
            [
                [
                    'M' => null,
                    'route' => null
                ],
                'web_layout'
            ],
            [
                [
                    'M' => 'foo',
                    'route' => null
                ],
                'foo'
            ],
            [
                [
                    'M' => null,
                    'route' => '/path',
                    'routePath' => '/path'
                ],
                '/path'
            ],
            [
                [
                    'M' => null,
                    'route' => '/path',
                    'routePath' => '/somethingelse'
                ],
                'web_layout'
            ]
        ];
    }

    /**
     * @param array $getParams
     * @param string $value
     * @dataProvider getModuleNameReturnsStringDataProvider
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::getModuleName
     */
    public function getModuleNameReturnsString(array $getParams, $value)
    {
        require_once(dirname(dirname(__FILE__)) . '/Fixtures/Utility/BackendUtilityFixture.php');
        if (!empty($getParams['route'])) {
            $router = GeneralUtility::makeInstance(Router::class);
            $router->addRoute($getParams['route'], new Route($getParams['routePath'], ['some', 'options']));
        }

        $_GET = $getParams;
        $this->assertSame($value, BackendUtilityFixture::getModuleNamePublic());
    }

    /**
     * Data Provider for getCurrentParametersReturnsArray()
     *
     * @return array
     */
    public function getCurrentParametersReturnsArrayDataProvider()
    {
        return [
            [
                ['a' => 'b', 'c' => 'd', 'e' => 'f'],
                ['a' => 'b', 'c' => 'd', 'e' => 'f'],
                false
            ],
            [
                ['a' => 'b', 'c' => 'd', 'M' => 'f'],
                ['a' => 'b', 'c' => 'd'],
                false
            ],
            [
                ['a' => 'b', 'moduleToken' => 'd', 'M' => 'f'],
                ['a' => 'b'],
                false
            ],
            [
                ['a' => 'b', 'moduleToken' => 'd', 'M' => 'f'],
                ['a' => 'b'],
                true
            ]
        ];
    }

    /**
     * @param array $getParameters
     * @param array $expectedResult
     * @param bool $injectAsGetParam
     * @dataProvider getCurrentParametersReturnsArrayDataProvider
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::getCurrentParameters
     */
    public function getCurrentParametersReturnsArray($getParameters, $expectedResult, $injectAsGetParam)
    {
        if ($injectAsGetParam === false) {
            $this->assertSame($expectedResult, BackendUtility::getCurrentParameters($getParameters));
        } else {
            $_GET = $getParameters;
            $this->assertSame($expectedResult, BackendUtility::getCurrentParameters([]));
        }
    }

    /**
     * Data Provider for getPidFromBackendPageReturnsInt()
     *
     * @return array
     */
    public function getPidFromBackendPageReturnsIntDataProvider()
    {
        return [
            'TYPO3 6.2 returnUrl' => [
                '/typo3/sysext/cms/layout/db_layout.php?id=17#element-tt_content-14&edit[tt_content][14]=edit',
                17
            ],
            'TYPO3 6.2 returnUrl II' => [
                '/typo3/sysext/cms/layout/db_layout.php?id=15#element-tt_content-34',
                15
            ],
            'TYPO3 7.6 returnUrl' => [
                '/typo3/index.php?M=web_layout&moduleToken=' .
                    'afcd9cc86e6cd393edac6a60c33f38f2c2b48721&id=15#element-tt_content-34',
                15
            ],
            'Any example' => [
                '&returnUrl=abc.html?id=1243&abc=123',
                1243
            ],
            'Any example II' => [
                '&returnUrl=abc.html?abc=1243&xyz=abc',
                0
            ],
            'Any example III' => [
                '',
                1514816014062
            ],
        ];
    }

    /**
     * @param string $returnUrl
     * @param int $expectedResult
     * @dataProvider getPidFromBackendPageReturnsIntDataProvider
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::getPidFromBackendPage
     */
    public function getPidFromBackendPageReturnsInt($returnUrl, $expectedResult)
    {
        if (empty($returnUrl)) {
            $_GET['returnUrl'] = '&returnUrl=sdaf.html?id=1514816014062&ied=abc';
        }
        $this->assertSame($expectedResult, BackendUtility::getPidFromBackendPage($returnUrl));
    }

    /**
     * @return void
     * @test
     * @covers ::getPagesTSconfig
     */
    public function getPagesTSconfigReturnsString()
    {
        $this->expectExceptionCode(1459422492);
        BackendUtility::getPagesTSconfig(1);
    }

    /**
     * @return void
     * @test
     * @covers ::filterPagesForAccess
     */
    public function filterPagesForAccessReturnsArray()
    {
        $GLOBALS['BE_USER']->user['admin'] = 1;
        $this->assertSame([1, 2], BackendUtility::filterPagesForAccess([1, 2]));

        $GLOBALS['BE_USER']->user['admin'] = 0;
        $this->expectExceptionCode(1203699034);
        BackendUtility::filterPagesForAccess([1, 2]);
    }

    /**
     * @return void
     * @test
     * @covers ::isBackendContext
     */
    public function isBackendContextReturnsBool()
    {
        define('TYPO3_MODE', 'BE');
        $this->assertTrue(BackendUtility::isBackendContext());
    }
}
