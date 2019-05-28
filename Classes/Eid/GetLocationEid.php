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
        return $address['route'] . ', ' . $address['locality'];
    }

    /**
     * Get Address from geo coordinates
     *      with service from nominatim.openstreetmap.org (since google needs an API key)
     *
     * @param float $lat
     * @param float $lng
     * @return array all location infos
     *        ['route'] = 'Kunstmuehlstr.';
     *        ['locality'] = 'Rosenheim';
     *        ['country'] = 'Germany';
     *        ['postal_code'] = '83026';
     */
    protected function getAddressFromGeo($lat, $lng): array
    {
        $result = [];
        $url = 'https://nominatim.openstreetmap.org/reverse?format=json&addressdetails=1&lat=' . $lat . '&lon=' . $lng;
        $json = GeneralUtility::getUrl($url);
        if ($json !== false) {
            $data = json_decode($json, true);
            if (!empty($data['address'])) {
                $result = [
                    'route' => (string)$data['address']['road'],
                    'locality' => (string)$data['address']['village'] ?: (string)$data['address']['town'],
                    'country' => (string)$data['address']['country'],
                    'postal_code' => (string)$data['address']['postcode']
                ];
            }
        }
        return $result;
    }
}

$eid = GeneralUtility::makeInstance(GetLocationEid::class);
// @extensionScannerIgnoreLine Seems to be a false positive ->main()
echo $eid->main();
