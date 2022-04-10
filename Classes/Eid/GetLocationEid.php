<?php
declare(strict_types = 1);
namespace In2code\Powermail\Eid;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class could called with AJAX via eID and
 * returns a location from geo coordinates
 */
class GetLocationEid
{
    /**
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * @var string
     */
    protected string $content = '';

    /**
     * Language settings for google maps
     *
     * @var string
     */
    protected string $language = 'en';

    /**
     * Generates the output
     */
    public function main(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;

        try {
            $address = $this->getAddressFromGeo(
                isset($this->request->getQueryParams()['lat']) ? (float)$this->request->getQueryParams()['lat'] : 0.0,
                isset($this->request->getQueryParams()['lng']) ? (float)$this->request->getQueryParams()['lng'] : 0.0,
            );
            if (empty($address)) {
                throw new Exception();
            }

            if (!empty($address['route'])) {
                $this->content .= $address['route'];
            }
            if (!empty($address['locality'])) {
                $this->content .= ', ' . $address['locality'];
            }
            if (!empty($address['country'])) {
                $this->content .= ', ' . $address['country'];
            }
            $response = new Response();
            $response->getBody()->write($this->content);
            return $response;
        } catch (InvalidArgumentException $e) {
            // add a 410 "gone" if invalid parameters given
            return (new Response())->withStatus(410);
        } catch (Exception $e) {
            return (new Response())->withStatus(404);
        }
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
    protected function getAddressFromGeo(float $lat, float $lng): array
    {
        $result = [];
        $url = 'https://nominatim.openstreetmap.org/reverse?format=json&addressdetails=1&lat=' . $lat . '&lon=' . $lng;

        try {
            $json = GeneralUtility::getUrl($url);
            if ($json !== false) {
                $data = json_decode($json, true);
                if (!empty($data['address'])) {
                    $locality = '';
                    if (isset($data['address']['village'])) {
                        $locality = (string)$data['address']['village'];
                    } elseif (isset($data['address']['town'])) {
                        $locality = (string)$data['address']['town'];
                    }

                    $result = [
                        'route' => isset($data['address']['road']) ? (string)$data['address']['road'] : '',
                        'locality' => $locality !== ''? $locality : '',
                        'country' => isset($data['address']['country']) ? (string)$data['address']['country'] : '',
                        'postal_code' => isset($data['address']['postcode']) ? (string)$data['address']['postcode'] : '',
                    ];
                }
            }
        } catch (Throwable $e) {
        }
        return $result;
    }
}
