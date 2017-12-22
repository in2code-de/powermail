<?php
namespace In2code\Powermail\Tests\ViewHelpers\Validation;

use In2code\Powermail\ViewHelpers\Validation\AbstractValidationViewHelper;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class AbstractValidationViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Validation\AbstractValidationViewHelper
 */
class AbstractValidationViewHelperTest extends UnitTestCase
{

    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $abstractValidationViewHelperMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->abstractValidationViewHelperMock = $this->getAccessibleMock(
            AbstractValidationViewHelper::class,
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->generalValidatorMock);
    }

    /**
     * Dataprovider for
     *        isNativeValidationEnabledReturnsBool()
     *        isClientValidationEnabledReturnsBool()
     *
     * @return array
     */
    public function isValidationEnabledReturnsBoolDataProvider()
    {
        return [
            'nativeAndClientActivated' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '1'
                    ]
                ],
                true,
                true
            ],
            'nativeOnlyActivated' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '0'
                    ]
                ],
                true,
                false
            ],
            'clientOnlyActivated' => [
                [
                    'validation' => [
                        'native' => '0',
                        'client' => '1'
                    ]
                ],
                false,
                true
            ],
            'nothingActivated' => [
                [
                    'validation' => [
                        'native' => '0',
                        'client' => '0'
                    ]
                ],
                false,
                false
            ],
        ];
    }

    /**
     * @param array $settings
     * @param bool $expectedNativeResult
     * @param bool $expectedClientResult
     * @return void
     * @dataProvider isValidationEnabledReturnsBoolDataProvider
     * @test
     * @covers ::isNativeValidationEnabled
     */
    public function isNativeValidationEnabledReturnsBool($settings, $expectedNativeResult, $expectedClientResult)
    {
        unset($expectedClientResult);
        $this->abstractValidationViewHelperMock->_set('settings', $settings);
        $result = $this->abstractValidationViewHelperMock->_callRef('isNativeValidationEnabled');
        $this->assertSame($expectedNativeResult, $result);
    }

    /**
     * @param array $settings
     * @param bool $expectedNativeResult
     * @param bool $expectedClientResult
     * @return void
     * @dataProvider isValidationEnabledReturnsBoolDataProvider
     * @test
     * @covers ::isClientValidationEnabled
     */
    public function isClientValidationEnabledReturnsBool($settings, $expectedNativeResult, $expectedClientResult)
    {
        unset($expectedNativeResult);
        $this->abstractValidationViewHelperMock->_set('settings', $settings);
        $result = $this->abstractValidationViewHelperMock->_callRef('isClientValidationEnabled');
        $this->assertSame($expectedClientResult, $result);
    }
}
