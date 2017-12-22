<?php
namespace In2code\Powermail\Tests\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Form;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class EnableParsleyAndAjaxViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Validation\EnableParsleyAndAjaxViewHelper
 */
class EnableParsleyAndAjaxViewHelperTest extends UnitTestCase
{

    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $enableParsleyAndAjaxViewHelperMock;

    /**
     * @return void
     */
    public function setUp()
    {
        require_once(dirname(dirname(dirname(__FILE__))) .
            '/Fixtures/ViewHelpers/Validation/EnableParsleyAndAjaxViewHelperFixture.php');
        $this->enableParsleyAndAjaxViewHelperMock = $this->getAccessibleMock(
            '\In2code\Powermail\Tests\Fixtures\ViewHelpers\Validation\EnableParsleyAndAjaxViewHelperFixture',
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->enableParsleyAndAjaxViewHelperMock);
    }

    /**
     * Dataprovider for render()
     *
     * @return array
     */
    public function renderReturnsArrayDataProvider()
    {
        return [
            'nativeAndClientAndAjaxAndNoAdditionalAttributes' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '1'
                    ],
                    'misc' => [
                        'ajaxSubmit' => '1'
                    ]
                ],
                [],
                [
                    'data-parsley-validate' => 'data-parsley-validate',
                    'data-validate' => 'html5',
                    'data-powermail-ajax' => 'true',
                    'data-powermail-form' => 123
                ]
            ],
            'clientAndAjaxAndNoAdditionalAttributes' => [
                [
                    'validation' => [
                        'native' => '0',
                        'client' => '1'
                    ],
                    'misc' => [
                        'ajaxSubmit' => '1'
                    ]
                ],
                [],
                [
                    'data-parsley-validate' => 'data-parsley-validate',
                    'data-powermail-ajax' => 'true',
                    'data-powermail-form' => 123
                ]
            ],
            'nativeAndAjaxAndNoAdditionalAttributes' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '0'
                    ],
                    'misc' => [
                        'ajaxSubmit' => '1'
                    ]
                ],
                [],
                [
                    'data-validate' => 'html5',
                    'data-powermail-ajax' => 'true',
                    'data-powermail-form' => 123
                ]
            ],
            'AjaxAndNoAdditionalAttributes' => [
                [
                    'validation' => [
                        'native' => '0',
                        'client' => '0'
                    ],
                    'misc' => [
                        'ajaxSubmit' => '1'
                    ]
                ],
                [],
                [
                    'data-powermail-ajax' => 'true',
                    'data-powermail-form' => 123
                ]
            ],
            'nativeAndClientAndNoAdditionalAttributes' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '1'
                    ],
                    'misc' => [
                        'ajaxSubmit' => '0'
                    ]
                ],
                [],
                [
                    'data-parsley-validate' => 'data-parsley-validate',
                    'data-validate' => 'html5'
                ]
            ],
            'nativeAndClientAndAjaxAndAdditionalAttributes' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '1'
                    ],
                    'misc' => [
                        'ajaxSubmit' => '1'
                    ]
                ],
                [
                    'www' => 'in2code.de',
                    'email' => 'service@in2code.de',
                    'data-uid' => 234
                ],
                [
                    'www' => 'in2code.de',
                    'email' => 'service@in2code.de',
                    'data-uid' => 234,
                    'data-parsley-validate' => 'data-parsley-validate',
                    'data-validate' => 'html5',
                    'data-powermail-ajax' => 'true',
                    'data-powermail-form' => 123
                ]
            ],
        ];
    }

    /**
     * @param array $settings
     * @param array $additionalAttributes
     * @param array $expectedResult
     * @return void
     * @dataProvider renderReturnsArrayDataProvider
     * @test
     * @covers ::render
     */
    public function renderReturnsArray($settings, $additionalAttributes, $expectedResult)
    {
        $form = new Form;
        $form->_setProperty('uid', 123);

        $this->enableParsleyAndAjaxViewHelperMock->_set('addRedirectUri', false);
        $this->enableParsleyAndAjaxViewHelperMock->_set('settings', $settings);
        $result = $this->enableParsleyAndAjaxViewHelperMock->_callRef('render', $form, $additionalAttributes);
        $this->assertSame($expectedResult, $result);
    }
}
