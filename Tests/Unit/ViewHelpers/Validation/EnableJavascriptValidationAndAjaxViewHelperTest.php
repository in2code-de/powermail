<?php

namespace In2code\Powermail\Tests\Unit\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Tests\Unit\Fixtures\ViewHelpers\Validation\EnableJavascriptValidationAndAjaxViewHelperFixture;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class EnableJavascriptValidationAndAjaxViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Validation\EnableJavascriptValidationAndAjaxViewHelper
 */
class EnableJavascriptValidationAndAjaxViewHelperTest extends UnitTestCase
{
    /**
     * @var EnableJavascriptValidationAndAjaxViewHelperFixture
     */
    protected $enableJavascriptValidationAndAjaxViewHelperMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->enableJavascriptValidationAndAjaxViewHelperMock = $this->getAccessibleMock(
            EnableJavascriptValidationAndAjaxViewHelperFixture::class,
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->enableJavascriptValidationAndAjaxViewHelperMock);
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
                        'client' => '1',
                    ],
                    'misc' => [
                        'ajaxSubmit' => '1',
                    ],
                ],
                [],
                [
                    'data-powermail-validate' => 'data-powermail-validate',
                    'data-validate' => 'html5',
                    'data-powermail-ajax' => 'true',
                    'data-powermail-form' => 123,
                ],
            ],
            'clientAndAjaxAndNoAdditionalAttributes' => [
                [
                    'validation' => [
                        'native' => '0',
                        'client' => '1',
                    ],
                    'misc' => [
                        'ajaxSubmit' => '1',
                    ],
                ],
                [],
                [
                    'data-powermail-validate' => 'data-powermail-validate',
                    'data-powermail-ajax' => 'true',
                    'data-powermail-form' => 123,
                ],
            ],
            'nativeAndAjaxAndNoAdditionalAttributes' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '0',
                    ],
                    'misc' => [
                        'ajaxSubmit' => '1',
                    ],
                ],
                [],
                [
                    'data-validate' => 'html5',
                    'data-powermail-ajax' => 'true',
                    'data-powermail-form' => 123,
                ],
            ],
            'AjaxAndNoAdditionalAttributes' => [
                [
                    'validation' => [
                        'native' => '0',
                        'client' => '0',
                    ],
                    'misc' => [
                        'ajaxSubmit' => '1',
                    ],
                ],
                [],
                [
                    'data-powermail-ajax' => 'true',
                    'data-powermail-form' => 123,
                ],
            ],
            'nativeAndClientAndNoAdditionalAttributes' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '1',
                    ],
                    'misc' => [
                        'ajaxSubmit' => '0',
                    ],
                ],
                [],
                [
                    'data-powermail-validate' => 'data-powermail-validate',
                    'data-validate' => 'html5',
                ],
            ],
            'nativeAndClientAndAjaxAndAdditionalAttributes' => [
                [
                    'validation' => [
                        'native' => '1',
                        'client' => '1',
                    ],
                    'misc' => [
                        'ajaxSubmit' => '1',
                    ],
                ],
                [
                    'www' => 'in2code.de',
                    'email' => 'service@in2code.de',
                    'data-uid' => 234,
                ],
                [
                    'www' => 'in2code.de',
                    'email' => 'service@in2code.de',
                    'data-uid' => 234,
                    'data-powermail-validate' => 'data-powermail-validate',
                    'data-validate' => 'html5',
                    'data-powermail-ajax' => 'true',
                    'data-powermail-form' => 123,
                ],
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
        $form = new Form();
        $form->_setProperty('uid', 123);

        $this->enableJavascriptValidationAndAjaxViewHelperMock->_set('addRedirectUri', false);
        $this->enableJavascriptValidationAndAjaxViewHelperMock->_set('settings', $settings);
        $arguments = [
            'form' => $form,
            'additionalAttributes' => $additionalAttributes,
        ];
        $this->enableJavascriptValidationAndAjaxViewHelperMock->_set('arguments', $arguments);
        $result = $this->enableJavascriptValidationAndAjaxViewHelperMock->_callRef('render');
        self::assertSame($expectedResult, $result);
    }
}
