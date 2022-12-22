<?php

namespace In2code\Powermail\Tests\Unit\Domain\Repository;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Tests\Helper\TestingHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class MailRepositoryTest
 * @coversDefaultClass \In2code\Powermail\Domain\Repository\MailRepository
 */
class MailRepositoryTest extends UnitTestCase
{
    /**
     * @var array
     */
    protected $testFilesToDelete = [];

    /**
     * @var MailRepository
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        TestingHelper::setDefaultConstants();
        $objectManager = TestingHelper::getObjectManager();
        $this->generalValidatorMock = $this->getAccessibleMock(MailRepository::class, ['dummy'], [$objectManager]);
    }

    /**
     * Dataprovider getLabelsWithMarkersFromMailReturnsArray()
     *
     * @return array
     */
    public function getLabelsWithMarkersFromMailReturnsArrayDataProvider()
    {
        return [
            [
                [
                    [
                        'marker',
                        'title',
                    ],
                ],
                [
                    'label_marker' => 'title',
                ],
            ],
            [
                [
                    [
                        'firstname',
                        'Firstname',
                    ],
                    [
                        'lastname',
                        'Lastname',
                    ],
                    [
                        'email',
                        'Email Address',
                    ],
                ],
                [
                    'label_firstname' => 'Firstname',
                    'label_lastname' => 'Lastname',
                    'label_email' => 'Email Address',
                ],
            ],
        ];
    }

    /**
     * @param array $values
     * @param string $expectedResult
     * @return void
     * @dataProvider getLabelsWithMarkersFromMailReturnsArrayDataProvider
     * @test
     * @covers ::getLabelsWithMarkersFromMail
     */
    public function getLabelsWithMarkersFromMailReturnsArray($values, $expectedResult)
    {
        $mail = new Mail();
        if (is_array($values)) {
            foreach ($values as $markerTitleMix) {
                $answer = new Answer();
                $field = new Field();
                $field->setMarker($markerTitleMix[0]);
                $field->setTitle($markerTitleMix[1]);
                $answer->setField($field);
                $mail->addAnswer($answer);
            }
        }

        $result = $this->generalValidatorMock->_callRef('getLabelsWithMarkersFromMail', $mail);
        self::assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider getVariablesWithMarkersFromMailReturnsArray()
     *
     * @return array
     */
    public function getVariablesWithMarkersFromMailReturnsArrayDataProvider()
    {
        return [
            [
                [
                    [
                        'marker',
                        'value',
                    ],
                ],
                [
                    'marker' => 'value',
                ],
            ],
            [
                [
                    [
                        'firstname',
                        'Alex',
                    ],
                    [
                        'lastname',
                        'Kellner',
                    ],
                    [
                        'email',
                        'alex@in2code.de',
                    ],
                ],
                [
                    'firstname' => 'Alex',
                    'lastname' => 'Kellner',
                    'email' => 'alex@in2code.de',
                ],
            ],
            [
                [
                    [
                        'checkbox',
                        [
                            'red',
                            'blue',
                        ],
                    ],
                    [
                        'firstname',
                        'Alex',
                    ],
                ],
                [
                    'checkbox' => 'red, blue',
                    'firstname' => 'Alex',
                ],
            ],
        ];
    }

    /**
     * @param array $values
     * @param string $expectedResult
     * @return void
     * @dataProvider getVariablesWithMarkersFromMailReturnsArrayDataProvider
     * @test
     * @covers ::getVariablesWithMarkersFromMail
     */
    public function getVariablesWithMarkersFromMailReturnsArray($values, $expectedResult)
    {
        $mail = new Mail();
        if (is_array($values)) {
            foreach ($values as $markerValueMix) {
                $answer = new Answer();
                $field = new Field();
                $field->setMarker($markerValueMix[0]);
                $answer->setValue($markerValueMix[1]);
                $answer->setValueType((is_array($markerValueMix[1]) ? 1 : 0));
                $answer->setField($field);
                $mail->addAnswer($answer);
            }
        }

        $this->generalValidatorMock->_callRef('disableSignals');
        $result = $this->generalValidatorMock->_callRef('getVariablesWithMarkersFromMail', $mail);
        self::assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider getSenderMailFromArgumentsReturnsString()
     *
     * @return array
     */
    public function getSenderMailFromArgumentsReturnsStringDataProvider()
    {
        return [
            [
                [
                    'no email',
                    'abc@def.gh',
                ],
                '',
                null,
                'abc@def.gh',
            ],
            [
                [
                    'alexander.kellner@in2code.de',
                    'abc@def.gh',
                ],
                '',
                null,
                'alexander.kellner@in2code.de',
            ],
            [
                [
                    'no email',
                ],
                'test1@email.org',
                'test2@email.org',
                'test1@email.org',
            ],
            [
                [
                    'no email',
                ],
                'test1@email.org',
                null,
                'test1@email.org',
            ],
            [
                [
                    'no email',
                ],
                '',
                'test2@email.org',
                'test2@email.org',
            ],
            [
                [
                    'abc',
                    'def',
                    'ghi',
                ],
                'test1@email.org',
                'test2@email.org',
                'test1@email.org',
            ],
        ];
    }

    /**
     * @param array $values
     * @param string $fallback
     * @param string $defaultMailFromAddress
     * @param string $expectedResult
     * @return void
     * @dataProvider getSenderMailFromArgumentsReturnsStringDataProvider
     * @test
     * @covers ::getSenderMailFromArguments
     */
    public function getSenderMailFromArgumentsReturnsString(
        $values,
        $fallback,
        $defaultMailFromAddress,
        $expectedResult
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] = $defaultMailFromAddress;
        $mail = new Mail();
        if (is_array($values)) {
            foreach ($values as $value) {
                $answer = new Answer();
                $answer->_setProperty('value', $value);
                $answer->_setProperty('valueType', (is_array($values) ? 2 : 0));
                $field = new Field();
                $field->setType('input');
                $field->setSenderEmail(true);
                $answer->setField($field);
                $mail->addAnswer($answer);
            }
        }

        $result = $this->generalValidatorMock->_callRef('getSenderMailFromArguments', $mail, $fallback);
        self::assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider getSenderNameFromArgumentsReturnsString()
     *
     * @return array
     */
    public function getSenderNameFromArgumentsReturnsStringDataProvider()
    {
        return [
            [
                [
                    'Alex',
                    'Kellner',
                ],
                null,
                null,
                'Alex Kellner',
            ],
            [
                [
                    'Prof. Dr.',
                    'Müller',
                ],
                'abc',
                'def',
                'Prof. Dr. Müller',
            ],
            [
                null,
                null,
                'Fallback Name',
                'Fallback Name',
            ],
            [
                null,
                'Fallback Name',
                null,
                'Fallback Name',
            ],
            [
                [
                    // test multivalue (e.g. checkbox)
                    [
                        'Prof.',
                        'Dr.',
                    ],
                    'Max',
                    'Muster',
                ],
                'xyz',
                'abc',
                'Prof. Dr. Max Muster',
            ],
        ];
    }

    /**
     * @param array $values
     * @param string $fallback
     * @param string $defaultMailFromAddress
     * @param string $expectedResult
     * @return void
     * @dataProvider getSenderNameFromArgumentsReturnsStringDataProvider
     * @test
     * @covers ::getSenderMailFromArguments
     */
    public function getSenderNameFromArgumentsReturnsString(
        $values,
        $fallback,
        $defaultMailFromAddress,
        $expectedResult
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'] = $defaultMailFromAddress;
        $mail = new Mail();
        if (is_array($values)) {
            foreach ($values as $value) {
                $answer = new Answer();
                $answer->_setProperty('translateFormat', 'Y-m-d');
                $answer->_setProperty('valueType', (is_array($values) ? 2 : 0));
                $field = new Field();
                $field->setType('input');
                $field->setSenderName(true);
                $answer->_setProperty('value', $value);
                $answer->setValueType((is_array($value) ? 1 : 0));
                $answer->setField($field);
                $mail->addAnswer($answer);
            }
        }

        $result = $this->generalValidatorMock->_callRef('getSenderNameFromArguments', $mail, $fallback);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @return void
     * @test
     * @covers ::cleanStringForQuery
     */
    public function cleanStringForQueryReturnsString()
    {
        $str = '1a2b3+üßT$st';
        $result = $this->generalValidatorMock->_call('cleanStringForQuery', $str);
        self::assertSame('1a2b3Tst', $result);
    }
}
