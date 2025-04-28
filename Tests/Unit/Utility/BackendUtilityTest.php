<?php

namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Tests\Helper\TestingHelper;
use In2code\Powermail\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class BackendUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\BackendUtility
 */
class BackendUtilityTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    /**
     * Data Provider for isBackendAdminReturnsBool()
     */
    public static function isBackendAdminReturnsBoolDataProvider(): array
    {
        return [
            [
                1,
                true,
            ],
            [
                0,
                false,
            ],
            [
                null,
                false,
            ],
        ];
    }

    /**
     * @param string $value
     * @param bool $expectedResult
     * @dataProvider isBackendAdminReturnsBoolDataProvider
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isBackendAdmin
     * @covers ::getBackendUserAuthentication
     */
    public function isBackendAdminReturnsBool($value, $expectedResult): void
    {
        TestingHelper::setDefaultConstants();
        $user = new BackendUserAuthentication();
        $GLOBALS['BE_USER'] = $user;

        if (is_int($value)) {
            $GLOBALS['BE_USER']->user['admin'] = $value;
        }

        self::assertSame($expectedResult, BackendUtility::isBackendAdmin());
    }

    /**
     * Data Provider for getPropertyFromBackendUserReturnsString()
     */
    public static function getPropertyFromBackendUserReturnsStringDataProvider(): array
    {
        return [
            [
                'admin',
                '1',
            ],
            [
                'warningMax',
                3,
            ],
            [
                '',
                '',
            ],
        ];
    }

    /**
     * @param string $property
     * @dataProvider getPropertyFromBackendUserReturnsStringDataProvider
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::getPropertyFromBackendUser
     * @covers ::getBackendUserAuthentication
     */
    public function getPropertyFromBackendUserReturnsString($property, mixed $value): void
    {
        TestingHelper::setDefaultConstants();
        $user = new BackendUserAuthentication();
        $GLOBALS['BE_USER'] = $user;

        if ($property !== null) {
            $GLOBALS['BE_USER']->user[$property] = $value;
        }

        self::assertSame($value, BackendUtility::getPropertyFromBackendUser($property));
    }

    /**
     * Data Provider for getCurrentParametersReturnsArray()
     */
    public static function getCurrentParametersReturnsArrayDataProvider(): array
    {
        return [
            [
                ['a' => 'b', 'c' => 'd', 'e' => 'f'],
                ['a' => 'b', 'c' => 'd', 'e' => 'f'],
                false,
            ],
            [
                ['a' => 'b', 'c' => 'd', 'M' => 'f'],
                ['a' => 'b', 'c' => 'd'],
                false,
            ],
            [
                ['a' => 'b', 'moduleToken' => 'd', 'M' => 'f'],
                ['a' => 'b'],
                false,
            ],
            [
                ['a' => 'b', 'moduleToken' => 'd', 'M' => 'f'],
                ['a' => 'b'],
                true,
            ],
        ];
    }

    /**
     * Data Provider for getPidFromBackendPageReturnsInt()
     */
    public static function getPidFromBackendPageReturnsIntDataProvider(): array
    {
        return [
            'TYPO3 6.2 returnUrl' => [
                '/typo3/sysext/cms/layout/db_layout.php?id=17#element-tt_content-14&edit[tt_content][14]=edit',
                17,
            ],
            'TYPO3 6.2 returnUrl II' => [
                '/typo3/sysext/cms/layout/db_layout.php?id=15#element-tt_content-34',
                15,
            ],
            'TYPO3 7.6 returnUrl' => [
                '/typo3/index.php?M=web_layout&moduleToken=' .
                    'afcd9cc86e6cd393edac6a60c33f38f2c2b48721&id=15#element-tt_content-34',
                15,
            ],
            'Any example' => [
                '&returnUrl=abc.html?id=1243&abc=123',
                1243,
            ],
            'Any example II' => [
                '&returnUrl=abc.html?abc=1243&xyz=abc',
                0,
            ],
            'Any example III' => [
                '',
                1514816014062,
            ],
        ];
    }

    /**
     * @test
     * @covers ::getPagesTSconfig
     * @throws DeprecatedException
     */
    public function getPagesTSconfigReturnsString(): void
    {
        self::assertEmpty(BackendUtility::getPagesTSconfig(1));
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::filterPagesForAccess
     */
    public function filterPagesForAccessReturnsArray(): void
    {
        TestingHelper::setDefaultConstants();
        $user = new BackendUserAuthentication();
        $GLOBALS['BE_USER'] = $user;

        $GLOBALS['BE_USER']->user['admin'] = 1;
        self::assertSame([1, 2], BackendUtility::filterPagesForAccess([1, 2]));
    }
}
