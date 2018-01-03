<?php
namespace In2code\Powermail\Tests\Unit\Controller;

use In2code\Powermail\Controller\FormController;
use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\Request;

/**
 * Class FormControllerTest
 * @coversDefaultClass \In2code\Powermail\Controller\FormController
 */
class FormControllerTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Controller\FormController
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            FormController::class,
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
                [],
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
     * @param bool $forwardActive
     * @return void
     * @dataProvider forwardIfFormParamsDoNotMatchReturnsVoidDataProvider
     * @test
     * @covers ::forwardIfFormParamsDoNotMatch
     */
    public function forwardIfFormParamsDoNotMatchReturnsVoid($arguments, $settings, $forwardActive)
    {
        $request = new Request();
        $request->setArguments($arguments);
        $this->generalValidatorMock->_set('request', $request);
        $this->generalValidatorMock->_set('settings', $settings);
        try {
            // if forward() is called, an exception will be thrown
            $this->generalValidatorMock->_callRef('forwardIfFormParamsDoNotMatch');
        } catch (\Exception $exception) {
            return;
        }
        $this->assertFalse($forwardActive);
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
}
