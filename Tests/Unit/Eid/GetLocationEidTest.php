<?php
namespace In2code\Powermail\Tests\Unit\Eid;

use In2code\Powermail\Eid\GetLocationEid;
use In2code\Powermail\Tests\Helper\TestingHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class GetLocationEidTest
 * @coversDefaultClass \In2code\Powermail\Eid\GetLocationEid
 */
class GetLocationEidTest extends UnitTestCase
{
    /**
     * @return void
     */
    public function setUp():void
    {
        TestingHelper::setDefaultConstants();
    }

    /**
     * @return array
     */
    public function mainDataProvider(): array
    {
        return [
            'in2code GmbH, Rosenheim, Germany' => [
                47.84787,
                12.113768,
                'Kunstmühlstraße'
            ],
            'Eisweiherweg, Pfaffing, Germany' => [
                48.0796126,
                12.0898908,
                'Eisweiherweg'
            ],
            'Baker Street, London, UK' => [
                51.5205573,
                -0.1566651,
                'Baker Street'
            ],
        ];
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param string $expectedResult
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @dataProvider mainDataProvider
     * @covers ::main
     * @covers ::getAddressFromGeo
     */
    public function testMain(float $latitude, float $longitude, string $expectedResult): void
    {
        $_GET['lat'] = $latitude;
        $_GET['lng'] = $longitude;
        $getLocationEid = new GetLocationEid();
        $this->assertContains($expectedResult, $getLocationEid->main());
    }
}
