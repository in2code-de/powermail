<?php

namespace In2code\Powermail\Tests\Unit\Controller;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\DataProcessor\DataProcessorRunner;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\UploadService;
use In2code\Powermail\Tests\Helper\TestingHelper;
use PHPUnit\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\ListenerProviderInterface;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
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
                new DataProcessorRunner(),
                $this->getMockBuilder(PersistenceManager::class)->disableOriginalConstructor()->getMock(),
            ]
        );
    }

    public function tearDown(): void
    {
        unset($this->generalValidatorMock);
        // $this->prophet->checkPredictions();
    }

    public static function forwardIfFormParamsDoNotMatchThrowsExceptionDataProvider(): array
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
        ];
    }

    /**
     * @dataProvider forwardIfFormParamsDoNotMatchThrowsExceptionDataProvider
     * @test
     * @covers ::forwardIfFormParamsDoNotMatch
     */
    public function forwardIfFormParamsDoNotMatchThrowsException(array $arguments, array $settings, bool $forward): void
    {
        $this->setDefaultControllerProperties($arguments);
        $this->generalValidatorMock->_set('settings', $settings);

        self::expectException(\TYPO3\CMS\Core\Http\PropagateResponseException::class);
        $this->generalValidatorMock->_call('forwardIfFormParamsDoNotMatch');
    }

    public static function forwardIfFormParamsDoNotMatchThrowsNoExceptionDataProvider(): array
    {
        $form = new Form();
        $form->_setProperty('uid', 2);

        $mail = new Mail();
        $mail->setForm($form);
        return [
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
     * @dataProvider forwardIfFormParamsDoNotMatchThrowsNoExceptionDataProvider
     * @test
     * @covers ::forwardIfFormParamsDoNotMatch
     */
    public function forwardIfFormParamsDoNotMatchThrowsNoException(array $arguments, array $settings, bool $forward): void
    {
        $this->setDefaultControllerProperties($arguments);
        $this->generalValidatorMock->_set('settings', $settings);

        try {
            $result = $this->generalValidatorMock->_call('forwardIfFormParamsDoNotMatch');
            self::assertSame($result, $forward);
        } catch (Exception $e) {
            self::fail('An exception was thrown when it should not have been: ' . $e->getMessage());
        }
    }

    public static function forwardIfMailParamEmptyDataProvider(): array
    {
        return [
            'no redirect, form param given' => [
                [
                    'form' => '1',
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
     * @covers ::forwardIfMailParamIsEmpty
     */
    public function forwardIfMailParamEmpty(array $arguments, bool $forward): void
    {
        TestingHelper::setDefaultConstants();
        $this->setDefaultControllerProperties($arguments);

        self::expectException(\TYPO3\CMS\Core\Http\PropagateResponseException::class);
        $this->generalValidatorMock->_call('forwardIfMailParamIsEmpty');
    }

    public static function forwardIfFormParamsDoNotMatchForOptinConfirmThrowsExceptionDataProvider(): array
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
        ];
    }

    /**
     * @dataProvider forwardIfFormParamsDoNotMatchForOptinConfirmThrowsExceptionDataProvider
     * @test
     * @covers ::forwardIfFormParamsDoNotMatchForOptinConfirm
     */
    public function forwardIfFormParamsDoNotMatchForOptinConfirmThrowsException(array $settings, int $formUid, bool $forward): void
    {
        TestingHelper::setDefaultConstants();
        $this->generalValidatorMock->_set('settings', $settings);
        $form = new Form();
        $form->_setProperty('uid', $formUid);

        $mail = new Mail();
        $mail->setForm($form);

        $this->generalValidatorMock->injectResponseFactory(new ResponseFactory());
        $this->generalValidatorMock->injectStreamFactory(new StreamFactory());

        self::expectException(\TYPO3\CMS\Core\Http\PropagateResponseException::class);
        $this->generalValidatorMock->_call('forwardIfFormParamsDoNotMatchForOptinConfirm', $mail);
    }

    public static function forwardIfFormParamsDoNotMatchForOptinConfirmThrowsNoExceptionDataProvider(): array
    {
        return [
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
     * @dataProvider forwardIfFormParamsDoNotMatchForOptinConfirmThrowsNoExceptionDataProvider
     * @test
     * @covers ::forwardIfFormParamsDoNotMatchForOptinConfirm
     */
    public function forwardIfFormParamsDoNotMatchForOptinConfirmThrowsNoException(array $settings, int $formUid, bool $forward): void
    {
        TestingHelper::setDefaultConstants();
        $this->generalValidatorMock->_set('settings', $settings);
        $form = new Form();
        $form->_setProperty('uid', $formUid);

        $mail = new Mail();
        $mail->setForm($form);

        $this->generalValidatorMock->injectResponseFactory(new ResponseFactory());
        $this->generalValidatorMock->injectStreamFactory(new StreamFactory());

        try {
            $result = $this->generalValidatorMock->_call('forwardIfFormParamsDoNotMatchForOptinConfirm', $mail);
            self::assertSame($result, $forward);
        } catch (Exception $e) {
            self::fail('An exception was thrown when it should not have been: ' . $e->getMessage());
        }
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
     * @dataProvider isMailPersistActiveReturnBoolDataProvider
     * @test
     * @covers ::isMailPersistActive
     */
    public function isMailPersistActiveReturnBool($store, $optin, $hash, $expectedResult): void
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
     * @test
     * @covers ::isNoOptin
     */
    public function isNoOptinReturnsBool(): void
    {
        $this->generalValidatorMock->_set('settings', []);
        self::assertTrue($this->generalValidatorMock->_call('isNoOptin', new Mail(), ''));
    }

    /**
     * @test
     * @covers ::isPersistActive
     */
    public function isPersistActiveReturnsBool(): void
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
     * @test
     * @covers ::isSenderMailEnabled
     */
    public function isSenderMailEnabledReturnsBool(): void
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
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
     */
    protected function setDefaultControllerProperties($arguments = []): void
    {
        $requestParameters = (new ExtbaseRequestParameters())->setArguments($arguments);
        $request = (new ServerRequest())->withAttribute('extbase', $requestParameters);
        foreach ($arguments as $key => $argument) {
            $request = $request->withAttribute($key, $arguments[$key]);
        }

        $this->generalValidatorMock->_set('request', new Request($request));
        $this->generalValidatorMock->_set('response', new Response());
        $this->generalValidatorMock->_set(
            'uriBuilder',
            $this->getMockBuilder(UriBuilder::class)->disableOriginalConstructor()->getMock()
        );
        $this->generalValidatorMock->_set('settings', ['staticTemplate' => '1']);
        $this->generalValidatorMock->_set('objectManager', TestingHelper::getObjectManager());
    }
}
