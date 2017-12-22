<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\BackendUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

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
        ];
    }

    /**
     * @param string $value
     * @param bool $expectedResult
     * @dataProvider isBackendAdminReturnsBoolDataProvider
     * @return void
     * @test
     * @covers ::isBackendAdmin
     */
    public function isBackendAdminReturnsBool($value, $expectedResult)
    {
        $GLOBALS['BE_USER']->user['admin'] = $value;
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
        ];
    }

    /**
     * @param string $property
     * @param mixed $value
     * @dataProvider getPropertyFromBackendUserReturnsStringDataProvider
     * @return void
     * @test
     * @covers ::getPropertyFromBackendUser
     */
    public function getPropertyFromBackendUserReturnsString($property, $value)
    {
        $GLOBALS['BE_USER']->user[$property] = $value;
        $this->assertSame($value, BackendUtility::getPropertyFromBackendUser($property));
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
                ['a' => 'b', 'c' => 'd', 'e' => 'f']
            ],
            [
                ['a' => 'b', 'c' => 'd', 'M' => 'f'],
                ['a' => 'b', 'c' => 'd']
            ],
            [
                ['a' => 'b', 'moduleToken' => 'd', 'M' => 'f'],
                ['a' => 'b']
            ],
        ];
    }

    /**
     * @param array $getParameters
     * @param array $expectedResult
     * @dataProvider getCurrentParametersReturnsArrayDataProvider
     * @return void
     * @test
     * @covers ::getCurrentParameters
     */
    public function getCurrentParametersReturnsArray($getParameters, $expectedResult)
    {
        $this->assertSame($expectedResult, BackendUtility::getCurrentParameters($getParameters));
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
                '/typo3/sysext/cms/layout/db_layout.php?' . 'id=17#element-tt_content-14&edit[tt_content][14]=edit',
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
        ];
    }

    /**
     * @param string $returnUrl
     * @param int $expectedResult
     * @dataProvider getPidFromBackendPageReturnsIntDataProvider
     * @return void
     * @test
     * @covers ::getPidFromBackendPage
     */
    public function getPidFromBackendPageReturnsInt($returnUrl, $expectedResult)
    {
        $this->assertSame($expectedResult, BackendUtility::getPidFromBackendPage($returnUrl));
    }
}
