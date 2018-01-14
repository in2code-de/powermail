<?php
declare(strict_types=1);
namespace In2code\Powermail\Eid;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class could called with AJAX via eID and
 * returns a location from geo coordinates
 */
class GetLocationEid
{

    /**
     * Language settings for google maps
     *
     * @var string
     */
    protected $language = 'en';

    /**
     * Generates the output
     *
     * @return string        from action
     */
    public function main()
    {
        $lat = GeneralUtility::_GP('lat');
        $lng = GeneralUtility::_GP('lng');

        $address = $this->getAddressFromGeo($lat, $lng);
        return $address['route'] . ' ' . $address['street_number'] . ', ' . $address['locality'];
    }

    /**
     * Get Address from geo coordinates
     *
     * @param float $lat
     * @param float $lng
     * @return array all location infos
     *        ['street_number'] = 12;
     *        ['route'] = 'Kunstmuehlstr.';
     *        ['locality'] = 'Rosenheim';
     *        ['country'] = 'Germany';
     *        ['postal_code'] = '83026';
     */
    protected function getAddressFromGeo($lat, $lng): array
    {
        $result = [];
        $json = GeneralUtility::getUrl(
            'https://maps.googleapis.com/maps/api/geocode/json' .
            '?sensor=false&language=' . $this->language . '&latlng=' . urlencode($lat . ',' . $lng)
        );
        if ($json !== false) {
            $jsonDecoded = json_decode($json, true);
            if (!empty($jsonDecoded['results'])) {
                foreach ((array)$jsonDecoded['results'][0]['address_components'] as $values) {
                    $result[$values['types'][0]] = $values['long_name'];
                }
            }
        }
        return $result;
    }
}

$eid = GeneralUtility::makeInstance(GetLocationEid::class);
// @extensionScannerIgnoreLine Seems to be a false positive ->main()
echo $eid->main();
