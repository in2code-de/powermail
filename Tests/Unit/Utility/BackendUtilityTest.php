<?php
namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Tests\Helper\TestingHelper;
use In2code\Powermail\Tests\Unit\Fixtures\Utility\BackendUtilityFixture;
use In2code\Powermail\Utility\BackendUtility;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\Route;
use TYPO3\CMS\Backend\Routing\Router;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;

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
     * @covers \In2code\Powermail\Utility\AbstractUtility::getBackendUserAuthentication
     */
    public function isBackendAdminReturnsBool($value, $expectedResult)
    {
        TestingHelper::setDefaultConstants();
        $user = new BackendUserAuthentication();
        $GLOBALS = [
            'BE_USER' => $user
        ];
        if (is_int($value)) {
            $GLOBALS['BE_USER']->user['admin'] = $value;
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
                '',
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
     * @covers \In2code\Powermail\Utility\AbstractUtility::getBackendUserAuthentication
     */
    public function getPropertyFromBackendUserReturnsString($property, $value)
    {
        TestingHelper::setDefaultConstants();
        $user = new BackendUserAuthentication();
        $GLOBALS = [
            'BE_USER' => $user
        ];
        if ($property !== null) {
            $GLOBALS['BE_USER']->user[$property] = $value;
        }
        $this->assertSame($value, BackendUtility::getPropertyFromBackendUser($property));
    }

    /**
     * @return void
     * @test
     * @covers ::createEditUri
     * @covers ::getReturnUrl
     * @covers ::getModuleName
     * @throws RouteNotFoundException
     */
    public function createEditUriReturnsString()
    {
        $this->expectExceptionCode(1476050190);
        BackendUtility::createEditUri('tt_content', 123);
    }

    /**
     * @return void
     * @test
     * @covers ::createNewUri
     * @covers ::getReturnUrl
     * @covers ::getModuleName
     * @throws RouteNotFoundException
     */
    public function createNewUriReturnsString()
    {
        $this->expectExceptionCode(1476050190);
        BackendUtility::createNewUri('tt_content', 123);
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
                'record_edit'
            ],
            [
                [
                    'M' => 'foo',
                    'route' => null
                ],
                'record_edit'
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
                'record_edit'
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
     * @throws DeprecatedException
     */
    public function getPagesTSconfigReturnsString()
    {
        $this->assertEmpty(BackendUtility::getPagesTSconfig(1));
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::filterPagesForAccess
     * @throws Exception
     */
    public function filterPagesForAccessReturnsArray()
    {
        TestingHelper::setDefaultConstants();
        $user = new BackendUserAuthentication();
        $GLOBALS = [
            'BE_USER' => $user
        ];

        $GLOBALS['BE_USER']->user['admin'] = 1;
        $this->assertSame([1, 2], BackendUtility::filterPagesForAccess([1, 2]));
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
