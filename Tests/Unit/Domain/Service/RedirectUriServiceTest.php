<?php

namespace In2code\Powermail\Tests\Unit\Domain\Service;

use In2code\Powermail\Tests\Unit\Fixtures\Domain\Service\RedirectUriServiceFixture;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class RedirectUriServiceTest
 * @coversDefaultClass \In2code\Powermail\Domain\Service\RedirectUriService
 */
class RedirectUriServiceTest extends UnitTestCase
{
    /**
     * @var RedirectUriServiceFixture
     */
    protected $generalValidatorMock;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            RedirectUriServiceFixture::class,
            ['dummy'],
            [new ContentObjectRenderer()]
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
     * Data Provider for getTargetFromFlexFormReturnString()
     *
     * @return array
     */
    public function getTargetFromFlexFormReturnStringDataProvider()
    {
        return [
            '234' => [
                [
                    'settings' => [
                        'flexform' => [
                            'thx' => [
                                'redirect' => '234',
                            ],
                        ],
                    ],
                ],
                '234',
            ],
            'test.jpg' => [
                [
                    'settings' => [
                        'flexform' => [
                            'thx' => [
                                'redirect' => 'fileadmin/test.jpg',
                            ],
                        ],
                    ],
                ],
                'fileadmin/test.jpg',
            ],
            'empty' => [
                [],
                null,
            ],
        ];
    }

    /**
     * @param array $flexFormArray
     * @param string $expectedResult
     * @dataProvider getTargetFromFlexFormReturnStringDataProvider
     * @return void
     * @test
     * @covers ::getTargetFromFlexForm
     */
    public function getTargetFromFlexFormReturnString($flexFormArray, $expectedResult)
    {
        $this->generalValidatorMock->_set('flexFormFixture', $flexFormArray);
        self::assertEquals($expectedResult, $this->generalValidatorMock->_call('getTargetFromFlexForm'));
    }
}
