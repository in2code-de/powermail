<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Utility\ObjectUtility;
use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Class CountriesFromStaticInfoTablesService
 */
class CountriesFromStaticInfoTablesService
{

    /**
     * Build array with countries
     *
     * @param string $key
     * @param string $value
     * @param string $sortbyField
     * @param string $sorting
     * @return array
     */
    public function getCountries(
        $key = 'isoCodeA3',
        $value = 'officialNameLocal',
        $sortbyField = 'isoCodeA3',
        $sorting = 'asc'
    ) {
        $countryRepository = ObjectUtility::getObjectManager()->get(CountryRepository::class);
        $countries = $countryRepository->findAllOrderedBy($sortbyField, $sorting);
        $countriesArray = [];
        foreach ($countries as $country) {
            /** @var $country \SJBR\StaticInfoTables\Domain\Model\Country */
            $countriesArray[ObjectAccess::getProperty($country, $key)] = ObjectAccess::getProperty($country, $value);
        }
        return $countriesArray;
    }
}
