<?php
namespace In2code\Powermail\Unit\Tests\Eid;

use In2code\Powermail\Eid\GetLocationEid;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class GetLocationEidTest
 * @coversDefaultClass \In2code\Powermail\Eid\GetLocationEid
 */
class GetLocationEidTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Eid\GetLocationEid
     */
    protected $getLocationEidMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->getLocationEidMock = $this->getAccessibleMock(
            GetLocationEid::class,
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->getLocationEidMock);
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
                [
                    'route' => 'Kunstmühlstraße',
                    'locality' => 'Rosenheim',
                    'country' => 'Deutschland',
                    'postal_code' => '83026'
                ]
            ],
            'Eisweiherweg, Forsting, Germany' => [
                48.0796126,
                12.0898908,
                [
                    'route' => 'Eisweiherweg',
                    'locality' => 'Pfaffing',
                    'country' => 'Deutschland',
                    'postal_code' => '83539'
                ]
            ],
            'Baker Street, London, UK' => [
                51.5205573,
                -0.1566651,
                [
                    'route' => 'Baker Street',
                    'locality' => '',
                    'country' => 'UK',
                    'postal_code' => 'W1U 6RJ'
                ]
            ],
        ];
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param array $expectedResult
     * @return void
     * @dataProvider getAddressFromGeoReturnsArrayDataProvider
     * @test
     * @covers ::getAddressFromGeo
     */
    public function getAddressFromGeoReturnsArray($latitude, $longitude, $expectedResult)
    {
        $address = $this->getLocationEidMock->_callRef('getAddressFromGeo', $latitude, $longitude);
        foreach (array_keys($expectedResult) as $expectedResultSingleKey) {
            $this->assertSame($expectedResult[$expectedResultSingleKey], $address[$expectedResultSingleKey]);
        }
    }
}
