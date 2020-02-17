<?php
namespace In2code\Powermail\Unit\Tests\Eid;

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
    public function setUp()
    {
        TestingHelper::setDefaultConstants();
    }

    /**
     * Dataprovider getAddressFromGeoReturnsArray()
     *
     * @return array
     */
    public function getAddressFromGeoReturnsArrayDataProvider()
    {
        return [
            'in2code GmbH, Rosenheim, Germany' => [
                47.84787,
                12.113768,
                'Kunstmühlstraße, Rosenheim, Deutschland'
            ],
            'Eisweiherweg, Pfaffing, Germany' => [
                48.0796126,
                12.0898908,
                'Eisweiherweg, Pfaffing, Deutschland'
            ],
            'Baker Street, London, UK' => [
                51.5205573,
                -0.1566651,
                'Baker Street, United Kingdom'
            ],
        ];
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param string $expectedResult
     * @return void
     * @dataProvider getAddressFromGeoReturnsArrayDataProvider
     * @covers ::main
     * @covers ::getAddressFromGeo
     */
    public function testMain($latitude, $longitude, $expectedResult)
    {
        $_GET['lat'] = $latitude;
        $_GET['lng'] = $longitude;
        $getLocationEid = new GetLocationEid();
        $this->assertSame($expectedResult, $getLocationEid->main());
    }
}
