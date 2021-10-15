<?php
namespace In2code\Powermail\Tests\Unit\Domain\Service;

use In2code\Powermail\Domain\Service\ExportService;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class ExportServiceTest
 * @coversDefaultClass \In2code\Powermail\Domain\Service\ExportService
 */
class ExportServiceTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Service\ExportService
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp():void
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            ExportService::class,
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown():void
    {
        unset($this->generalValidatorMock);
    }

    /**
     * Data Provider for getRelativeTemplatePathAndFileNameReturnsString()
     *
     * @return array
     */
    public function getRelativeTemplatePathAndFileNameReturnsStringDataProvider()
    {
        return [
            [
                'csv',
                'Module/ExportCsv.html'
            ],
            [
                'xls',
                'Module/ExportXls.html'
            ],
            [
                'bullshit',
                'Module/ExportXls.html'
            ],
            [
                '',
                'Module/ExportXls.html'
            ],
        ];
    }

    /**
     * @param string $format
     * @param string $expectedResult
     * @dataProvider getRelativeTemplatePathAndFileNameReturnsStringDataProvider
     * @return void
     * @test
     * @covers ::getRelativeTemplatePathAndFileName
     */
    public function getRelativeTemplatePathAndFileNameReturnsString($format, $expectedResult)
    {
        $this->generalValidatorMock->setFormat($format);
        $this->assertSame($this->generalValidatorMock->_call('getRelativeTemplatePathAndFileName'), $expectedResult);
    }

    /**
     * Data Provider for getFormatReturnsString()
     *
     * @return array
     */
    public function getFormatReturnsStringDataProvider()
    {
        return [
            [
                'csv',
                'csv'
            ],
            [
                'xls',
                'xls'
            ],
            [
                '',
                'xls'
            ],
            [
                'XLS',
                'xls'
            ],
            [
                'CSV',
                'xls'
            ],
        ];
    }

    /**
     * @param string $format
     * @param string $expectedResult
     * @dataProvider getFormatReturnsStringDataProvider
     * @return void
     * @test
     * @covers ::getFormat
     */
    public function getFormatReturnsString($format, $expectedResult)
    {
        $this->generalValidatorMock->setFormat($format);
        $this->assertSame($this->generalValidatorMock->_call('getFormat'), $expectedResult);
    }
}
