<?php

namespace In2code\Powermail\Tests\Unit\Controller;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\UploadService;
use In2code\Powermail\Tests\Helper\TestingHelper;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\ListenerProviderInterface;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class FormControllerTest
 * @coversDefaultClass \In2code\Powermail\Controller\FormController
 */
class FormControllerTest extends UnitTestCase
{
    /**
     * @var AccessibleObjectInterface|MockObject
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $listenerProviderMock = $this->getMockBuilder(ListenerProviderInterface::class)->getMock();
        $eventDispatcher = new EventDispatcher($listenerProviderMock);

        $this->generalValidatorMock = $this->getAccessibleMock(
            FormController::class,
            null,
            [
                new FormRepository(),
                new FieldRepository(),
                new MailRepository(),
                $this->getMockBuilder(UploadService::class)->disableOriginalConstructor()->getMock(),
                $eventDispatcher,
            ]
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->generalValidatorMock);
        // $this->prophet->checkPredictions();
    }

    public static function forwardIfFormParamsDoNotMatchReturnsVoidDataProvider(): array
    {
        $form = new Form();
        $form->_setProperty('uid', 2);
        $mail = new Mail();
        $mail->setForm($form);
        return [
            'not allowed form given, forward' => [
                [
                    'mail' => [
                        'form' => '1',
                    ],
                ],
                [
                    'main' => [
                        'form' => '2,3',
                    ],
                ],
                true,
            ],
            'allowed form given, do not forward' => [
                [
                    'mail' => [
                        'form' => '1',
                    ],
                ],
                [
                    'main' => [
                        'form' => '1,2,3',
                    ],
                ],
                false,
            ],
            'mail object given, do not forward' => [
                [
                    'mail' => $mail,
                ],
                [
                    'main' => [
                        'form' => '2,3',
                    ],
                ],
                false,
            ],
            'nothing given, do not forward' => [
                [
                    'mail' => null,
                ],
                [
                    'main' => [
                        'form' => '2,3',
                    ],
                ],
                false,
            ],
        ];
    }

    /**
     * @dataProvider forwardIfFormParamsDoNotMatchReturnsVoidDataProvider
     * @test
     * @covers ::forwardIfFormParamsDoNotMatch
     */
    public function forwardIfFormParamsDoNotMatchReturnsVoid(array $arguments, array $settings, bool $forward): void
    {
        $this->setDefaultControllerProperties($arguments);
        $this->generalValidatorMock->_set('settings', $settings);

        // TODO: Check for redirect here
        // if ($forward === true) {
        //    $this->expectException(StopActionException::class);
        // }

        $response = $this->generalValidatorMock->_call('forwardIfFormParamsDoNotMatch');
        self::assertNull($response);
    }

    public static function forwardIfMailParamEmptyDataProvider(): array
    {
        return [
            'no redirect, form param given' => [
                [
                    'mail' => [
                        'form' => '1',
                    ],
                ],
                false,
            ],
            'redirect, form param is missing' => [
                [],
                true,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider forwardIfMailParamEmptyDataProvider
     * @test
     * @covers ::forwardIfMailParamEmpty
     */
    public function forwardIfMailParamEmpty(array $arguments, bool $forward): void
    {
        TestingHelper::setDefaultConstants();
        $this->setDefaultControllerProperties($arguments);

        $response = $this->generalValidatorMock->_call('forwardIfMailParamEmpty');
        if ($forward === true) {
            self::assertInstanceOf(ForwardResponse::class, $response);
        }
        self::assertTrue(true);
    }

    public static function forwardIfFormParamsDoNotMatchForOptinConfirmDataProvider(): array
    {
        return [
            'redirect, wrong form uid' => [
                [
                    'main' => [
                        'form' => '55,6,7',
                    ],
                ],
                5,
                true,
            ],
            'no redirect, correct form uid' => [
                [
                    'main' => [
                        'form' => '55,6,7',
                    ],
                ],
                6,
                false,
            ],
        ];
    }

    /**
     * @dataProvider forwardIfFormParamsDoNotMatchForOptinConfirmDataProvider
     * @test
     * @covers ::forwardIfFormParamsDoNotMatchForOptinConfirm
     */
    public function forwardIfFormParamsDoNotMatchForOptinConfirm(array $settings, int $formUid, bool $forward): void
    {
        TestingHelper::setDefaultConstants();
        $this->generalValidatorMock->_set('settings', $settings);
        $form = new Form();
        $form->_setProperty('uid', $formUid);
        $mail = new Mail();
        $mail->setForm($form);

        $this->generalValidatorMock->injectResponseFactory(new ResponseFactory());
        $this->generalValidatorMock->injectStreamFactory(new StreamFactory());
        $response = $this->generalValidatorMock->_call('forwardIfFormParamsDoNotMatchForOptinConfirm', $mail);
        if ($forward === true) {
            self::assertInstanceOf(ForwardResponse::class, $response);
        }
        self::assertTrue(true);
    }

    public static function isMailPersistActiveReturnBoolDataProvider(): array
    {
        return [
            'store 0, optin 0, hash NULL' => [
                '0',
                '0',
                '',
                false,
            ],
            'store 0, optin 0, hash NOTNULL' => [
                '0',
                '0',
                'abc',
                false,
            ],
            'store 0, optin 1, hash NULL' => [
                '0',
                '1',
                '',
                true,
            ],
            'store 0, optin 1, hash NOTNULL' => [
                '0',
                '1',
                'abc',
                false,
            ],
            'store 1, optin 0, hash NULL' => [
                '1',
                '0',
                '',
                true,
            ],
            'store 1, optin 0, hash NOTNULL' => [
                '1',
                '0',
                'abc',
                false,
            ],
            'store 1, optin 1, hash NULL' => [
                '1',
                '1',
                '',
                true,
            ],
            'store 1, optin 1, hash NOTNULL' => [
                '1',
                '1',
                'abc',
                false,
            ],
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
                'enable' => $store,
            ],
            'main' => [
                'optin' => $optin,
            ],
        ];
        $this->generalValidatorMock->_set('settings', $settings);
        self::assertSame($expectedResult, $this->generalValidatorMock->_call('isMailPersistActive', $hash));
    }

    /**
     * @return void
     * @test
     * @covers ::isNoOptin
     */
    public function isNoOptinReturnsBool()
    {
        $this->generalValidatorMock->_set('settings', []);
        self::assertTrue($this->generalValidatorMock->_call('isNoOptin', new Mail(), ''));
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
                'enable' => '1',
            ],
        ];
        $this->generalValidatorMock->_set('settings', $settings);
        self::assertTrue($this->generalValidatorMock->_call('isPersistActive'));
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
                'enable' => '1',
            ],
        ];
        $this->generalValidatorMock->_set('settings', $settings);
        self::assertTrue($this->generalValidatorMock->_call('isSenderMailEnabled'));
    }

    /**
     * @test
     * @covers ::isReceiverMailEnabled
     */
    public function isReceiverMailEnabledReturnsBool(): void
    {
        $settings = [
            'receiver' => [
                'enable' => '1',
            ],
        ];
        $this->generalValidatorMock->_set('settings', $settings);
        self::assertTrue($this->generalValidatorMock->_call('isReceiverMailEnabled'));
    }

    /**
     * @return void
     */
    protected function setDefaultControllerProperties($arguments = [])
    {
        $request = (new ServerRequest())->withAttribute('extbase', new ExtbaseRequestParameters());
        foreach ($arguments as $key => $argument) {
            $request = $request->withAttribute($key, $arguments[$key]);
        }
        $this->generalValidatorMock->_set('request', new Request($request));
        $this->generalValidatorMock->_set('response', new Response());
        $this->generalValidatorMock->_set('uriBuilder', new UriBuilder());
        $this->generalValidatorMock->_set('settings', ['staticTemplate' => '1']);
        $this->generalValidatorMock->_set('objectManager', TestingHelper::getObjectManager());
    }
}
