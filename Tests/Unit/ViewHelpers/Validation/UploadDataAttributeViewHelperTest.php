<?php

namespace In2code\Powermail\Tests\Unit\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\ViewHelpers\Validation\UploadAttributesViewHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class UploadDataAttributeViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Validation\UploadAttributesViewHelper
 */
class UploadDataAttributeViewHelperTest extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $abstractValidationViewHelperMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->abstractValidationViewHelperMock = $this->getAccessibleMock(
            UploadAttributesViewHelper::class,
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->generalValidatorMock);
    }

    /**
     * Dataprovider for renderReturnsArray()
     *
     * @return array
     */
    public function renderReturnsArrayDataProvider()
    {
        return [
            [
                [],
                [],
                [],
                [],
            ],
            [
                [],
                [
                    'marker' => 'firstname',
                ],
                [
                    'data-additional' => 'abc',
                ],
                [
                    'data-additional' => 'abc',
                ],
            ],
            [
                [
                    'misc' => [
                        'file' => [
                            'extension' => 'jpg,gif',
                        ],
                    ],
                ],
                [
                    'marker' => 'firstname',
                ],
                [
                    'data-additional' => 'true',
                ],
                [
                    'data-additional' => 'true',
                    'accept' => '.jpg,.gif',
                ],
            ],
            [
                [
                    'misc' => [
                        'file' => [
                            'extension' => 'jpg,gif',
                            'size' => '123456',
                        ],
                    ],
                ],
                [
                    'marker' => 'firstname',
                    'multiselect' => true,
                ],
                [
                    'data-additional' => 'true',
                ],
                [
                    'data-additional' => 'true',
                    'multiple' => 'multiple',
                    'accept' => '.jpg,.gif',
                ],
            ],
            [
                [
                    'misc' => [
                        'file' => [
                            'extension' => 'jpg,gif',
                            'size' => '123456',
                        ],
                    ],
                    'validation' => [
                        'client' => '1',
                    ],
                ],
                [
                    'marker' => 'firstname',
                    'multiselect' => true,
                ],
                [
                    'data-additional' => 'true',
                ],
                [
                    'data-additional' => 'true',
                    'multiple' => 'multiple',
                    'accept' => '.jpg,.gif',
                    'data-powermail-powermailfilesize' => '123456,firstname',
                    'data-powermail-powermailfilesize-message' => 'validationerror_upload_size',
                    'data-powermail-powermailfileextensions' => 'firstname',
                    'data-powermail-powermailfileextensions-message' => 'validationerror_upload_extension',
                ],
            ],
        ];
    }

    /**
     * @param array $settings
     * @param array $fieldProperties
     * @param array $additionalAttributes
     * @param array $expectedResult
     * @return void
     * @dataProvider renderReturnsArrayDataProvider
     * @test
     * @covers ::render
     */
    public function renderReturnsArray($settings, $fieldProperties, $additionalAttributes, $expectedResult)
    {
        $field = new Field();
        foreach ($fieldProperties as $propertyName => $propertyValue) {
            $field->_setProperty($propertyName, $propertyValue);
        }
        $this->abstractValidationViewHelperMock->_set('settings', $settings);
        $arguments = [
            'field' => $field,
            'additionalAttributes' => $additionalAttributes,
        ];
        $this->abstractValidationViewHelperMock->_set('arguments', $arguments);
        $result = $this->abstractValidationViewHelperMock->_callRef('render');
        self::assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider for getDottedListOfExtensions()
     *
     * @return array
     */
    public function getDottedListOfExtensionsReturnsStringDataProvider()
    {
        return [
            [
                'jpg,gif,jpeg',
                '.jpg,.gif,.jpeg',
            ],
            [
                '',
                '',
            ],
            [
                'php',
                '.php',
            ],
            [
                'jpg,gif,jpeg,doc,docx,xls,xlsx',
                '.jpg,.gif,.jpeg,.doc,.docx,.xls,.xlsx',
            ],
        ];
    }

    /**
     * @param string $string
     * @param string $expectedResult
     * @return void
     * @dataProvider getDottedListOfExtensionsReturnsStringDataProvider
     * @test
     * @covers ::getDottedListOfExtensions
     */
    public function getDottedListOfExtensionsReturnsString($string, $expectedResult)
    {
        $result = $this->abstractValidationViewHelperMock->_callRef('getDottedListOfExtensions', $string);
        self::assertSame($expectedResult, $result);
    }
}
