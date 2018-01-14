<?php
namespace In2code\Powermail\Tests\Unit\Controller;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\DataProcessor\DataProcessorRunner;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Tests\Helper\TestingHelper;
use In2code\Powermail\Tests\Unit\Fixtures\Controller\FormControllerFixture;
use In2code\Powermail\Tests\Unit\Fixtures\Domain\Repository\MailRepositoryFixture;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Response;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * Class FormControllerTest
 * @coversDefaultClass \In2code\Powermail\Controller\FormController
 */
class FormControllerTest extends UnitTestCase
{

    /**
     * @var FormController|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            FormControllerFixture::class,
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
     * @return void
     * @covers ::optinConfirmAction
     */
    public function testOptinConfirmAction()
    {
        //$formController = new FormController();
        //$formReflection = new \ReflectionClass($formController);
        //$mailRepoProp = $formReflection->getProperty('mailRepository');
        //$mailRepoProp->setAccessible(true);
        //$mailRepoProp->setValue(new MailRepositoryFixture(TestingHelper::getObjectManager()), 'mailRepository');

        ///**
        // * @var FormController|\PHPUnit_Framework_MockObject_MockObject $formController
        // */
        //$formController = $this
        //    ->getMockBuilder($formController::class)
        //    ->setMethods(['signalDispatch'])
        //    ->getMock();
        //$formController->method('signalDispatch')->willReturn(true);
        //$formController->optinConfirmAction(1, '');
        //$class->optinConfirmAction(123, '');
    }

    /**
     * @return void
     * @covers ::initializeAction
     */
    public function testInitializeAction()
    {
        $this->setDefaultControllerProperties();
        $this->generalValidatorMock->_call('initializeAction');
        $this->assertObjectHasAttribute('settings', $this->generalValidatorMock);
        $this->assertObjectHasAttribute('objectManager', $this->generalValidatorMock);
        $this->assertObjectHasAttribute('request', $this->generalValidatorMock);
    }

    /**
     * Dataprovider forwardIfFormParamsDoNotMatchReturnsVoid()
     *
     * @return array
     */
    public function forwardIfFormParamsDoNotMatchReturnsVoidDataProvider()
    {
        return [
            'not allowed form given, forward' => [
                [
                    'mail' => [
                        'form' => '1'
                    ]
                ],
                [
                    'main' => [
                        'form' => '2,3'
                    ]
                ],
                true
            ],
            'allowed form given, do not forward' => [
                [
                    'mail' => [
                        'form' => '1'
                    ]
                ],
                [
                    'main' => [
                        'form' => '1,2,3'
                    ]
                ],
                false
            ],
            'mail object given, do not forward' => [
                [
                    'mail' => new Mail()
                ],
                [
                    'main' => [
                        'form' => '2,3'
                    ]
                ],
                false
            ],
            'nothing given, do not forward' => [
                [
                    'mail' => null
                ],
                [
                    'main' => [
                        'form' => '2,3'
                    ]
                ],
                false
            ],
        ];
    }

    /**
     * @param array $arguments
     * @param array $settings
     * @param bool $forward
     * @return void
     * @dataProvider forwardIfFormParamsDoNotMatchReturnsVoidDataProvider
     * @test
     * @covers ::forwardIfFormParamsDoNotMatch
     */
    public function forwardIfFormParamsDoNotMatchReturnsVoid($arguments, $settings, $forward)
    {
        $this->setDefaultControllerProperties($arguments);
        $this->generalValidatorMock->_set('settings', $settings);
        if ($forward === true) {
            $this->expectExceptionCode(1514993039679);
        }
        $this->generalValidatorMock->_callRef('forwardIfFormParamsDoNotMatch');
    }

    /**
     * Dataprovider forwardIfMailParamEmpty()
     *
     * @return array
     */
    public function forwardIfMailParamEmptyDataProvider()
    {
        return [
            'no redirect, form param given' => [
                [
                    'mail' => [
                        'form' => '1'
                    ]
                ],
                false
            ],
            'redirect, form param is missing' => [
                [],
                true
            ]
        ];
    }

    /**
     * @param array $arguments
     * @param bool $forward
     * @return void
     * @dataProvider forwardIfMailParamEmptyDataProvider
     * @test
     * @covers ::forwardIfMailParamEmpty
     */
    public function forwardIfMailParamEmpty($arguments, $forward)
    {
        TestingHelper::setDefaultConstants();
        $this->setDefaultControllerProperties($arguments);
        if ($forward === true) {
            $this->expectExceptionCode(1514993039679);
        }
        $this->generalValidatorMock->_call('forwardIfMailParamEmpty');
    }

    /**
     * Dataprovider forwardIfFormParamsDoNotMatchForOptinConfirm()
     *
     * @return array
     */
    public function forwardIfFormParamsDoNotMatchForOptinConfirmDataProvider()
    {
        return [
            'redirect, wrong form uid' => [
                [
                    'main' => [
                        'form' => '55,6,7'
                    ]
                ],
                5,
                true
            ],
            'no redirect, correct form uid' => [
                [
                    'main' => [
                        'form' => '55,6,7'
                    ]
                ],
                6,
                false
            ]
        ];
    }

    /**
     * @param array $settings
     * @param int $formUid
     * @param bool $forward
     * @return void
     * @dataProvider forwardIfFormParamsDoNotMatchForOptinConfirmDataProvider
     * @test
     * @covers ::forwardIfFormParamsDoNotMatchForOptinConfirm
     */
    public function forwardIfFormParamsDoNotMatchForOptinConfirm(array $settings, $formUid, $forward)
    {
        TestingHelper::setDefaultConstants();
        $this->generalValidatorMock->_set('settings', $settings);
        $form = new Form();
        $form->_setProperty('uid', $formUid);
        $mail = new Mail();
        $mail->setForm($form);
        if ($forward === true) {
            $this->expectExceptionCode(1514993039679);
        }
        $this->generalValidatorMock->_call('forwardIfFormParamsDoNotMatchForOptinConfirm', $mail);
    }

    /**
     * Dataprovider isMailPersistActiveReturnBool()
     *
     * @return array
     */
    public function isMailPersistActiveReturnBoolDataProvider()
    {
        return [
            'store 0, optin 0, hash NULL' => [
                '0',
                '0',
                null,
                false
            ],
            'store 0, optin 0, hash NOTNULL' => [
                '0',
                '0',
                'abc',
                false
            ],
            'store 0, optin 1, hash NULL' => [
                '0',
                '1',
                null,
                true
            ],
            'store 0, optin 1, hash NOTNULL' => [
                '0',
                '1',
                'abc',
                false
            ],
            'store 1, optin 0, hash NULL' => [
                '1',
                '0',
                null,
                true
            ],
            'store 1, optin 0, hash NOTNULL' => [
                '1',
                '0',
                'abc',
                false
            ],
            'store 1, optin 1, hash NULL' => [
                '1',
                '1',
                null,
                true
            ],
            'store 1, optin 1, hash NOTNULL' => [
                '1',
                '1',
                'abc',
                false
            ]
        ];
    }

    /**
     * @param int $store
     * @param int $optin
     * @param string|null $hash
     * @param bool $expectedResult
     * @return void
     * @dataProvider isMailPersistActiveReturnBoolDataProvider
     * @test
     * @covers ::isMailPersistActive
     */
    public function isMailPersistActiveReturnBool($store, $optin, $hash, $expectedResult)
    {
        $settings = [
            'db' => [
                'enable' => $store
            ],
            'main' => [
                'optin' => $optin
            ]
        ];
        $this->generalValidatorMock->_set('settings', $settings);
        $this->assertSame($expectedResult, $this->generalValidatorMock->_callRef('isMailPersistActive', $hash));
    }

    /**
     * @return void
     * @test
     * @covers ::isNoOptin
     */
    public function isNoOptinReturnsBool()
    {
        $this->generalValidatorMock->_set('settings', []);
        $this->assertTrue($this->generalValidatorMock->_call('isNoOptin', new Mail(), ''));
    }

    /**
     * @return void
     * @test
     * @covers ::isPersistActive
     */
    public function isPersistActiveReturnsBool()
    {
        $settings = [
            'db' => [
                'enable' => '1'
            ]
        ];
        $this->generalValidatorMock->_set('settings', $settings);
        $this->assertTrue($this->generalValidatorMock->_call('isPersistActive'));
    }

    /**
     * @return void
     * @test
     * @covers ::isSenderMailEnabled
     */
    public function isSenderMailEnabledReturnsBool()
    {
        $settings = [
            'sender' => [
                'enable' => '1'
            ]
        ];
        $this->generalValidatorMock->_set('settings', $settings);
        $this->assertTrue($this->generalValidatorMock->_call('isSenderMailEnabled'));
    }

    /**
     * @return void
     * @test
     * @covers ::isReceiverMailEnabled
     */
    public function isReceiverMailEnabledReturnsBool()
    {
        $settings = [
            'receiver' => [
                'enable' => '1'
            ]
        ];
        $this->generalValidatorMock->_set('settings', $settings);
        $this->assertTrue($this->generalValidatorMock->_call('isReceiverMailEnabled'));
    }

    /**
     * @return void
     * @test
     * @covers ::injectDataProcessorRunner
     */
    public function injectDataProcessorRunner()
    {
        $dataProcessorRunner = new DataProcessorRunner();
        $this->generalValidatorMock->_call('injectDataProcessorRunner', $dataProcessorRunner);
        $this->assertAttributeInstanceOf(
            DataProcessorRunner::class,
            'dataProcessorRunner',
            $this->generalValidatorMock
        );
    }

    /**
     * @return void
     */
    protected function setDefaultControllerProperties($arguments = [])
    {
        $request = new Request();
        $request->setArguments($arguments);
        $this->generalValidatorMock->_set('request', $request);
        $this->generalValidatorMock->_set('response', new Response());
        $this->generalValidatorMock->_set('uriBuilder', new UriBuilder());
        $this->generalValidatorMock->_set('settings', ['staticTemplate' => '1']);
        $this->generalValidatorMock->_set('objectManager', TestingHelper::getObjectManager());
    }
}
