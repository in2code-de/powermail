<?php

declare(strict_types=1);

namespace In2code\Powermail\ViewHelpers\Form;

use In2code\Powermail\Domain\Service\CountriesFromStaticInfoTablesService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CountriesViewHelper
 */
class CountriesViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('key', 'string', 'key', false, 'isoCodeA3');
        $this->registerArgument('value', 'string', 'value', false, 'officialNameLocal');
        $this->registerArgument('sortbyField', 'string', 'sortbyField', false, 'isoCodeA3');
        $this->registerArgument('sorting', 'string', 'sorting', false, 'asc');
    }

    /**
     * Get array with countries
     *
     * @return array
     * @throws PropertyNotAccessibleException
     */
    public function render(): array
    {
        $countries = $this->getCountries();

        if (ExtensionManagementUtility::isLoaded('static_info_tables')) {
            $key = $this->arguments['key'];
            $value = $this->arguments['value'];
            $sortbyField = $this->arguments['sortbyField'];
            $sorting = $this->arguments['sorting'];
            $countriesService = GeneralUtility::makeInstance(CountriesFromStaticInfoTablesService::class);
            $countries = $countriesService->getCountries($key, $value, $sortbyField, $sorting);
        }

        return $countries;
    }

    /**
     * Build an country array
     *
     * @return array
     */
    protected function getCountries(): array
    {
        $countries = [
            'AND' => 'Andorra',
            'ARE' => 'الإمارات العربيّة المتّحدة',
            'AFG' => 'افغانستان',
            'ATG' => 'Antigua and Barbuda',
            'AIA' => 'Anguilla',
            'ALB' => 'Shqipëria',
            'ARM' => 'Հայաստան',
            'ANT' => 'Nederlandse Antillen',
            'AGO' => 'Angola',
            'ATA' => 'Antarctica',
            'ARG' => 'Argentina',
            'ASM' => 'Amerika Samoa',
            'AUT' => 'Österreich',
            'AUS' => 'Australia',
            'ABW' => 'Aruba',
            'AZE' => 'Azərbaycan',
            'BIH' => 'BiH/БиХ',
            'BRB' => 'Barbados',
            'BGD' => 'বাংলাদেশ',
            'BEL' => 'Belgique',
            'BFA' => 'Burkina',
            'BGR' => 'България (Bulgaria)',
            'BHR' => 'البحري',
            'BDI' => 'Burundi',
            'BEN' => 'Bénin',
            'BMU' => 'Bermuda',
            'BRN' => 'دارالسلام',
            'BOL' => 'Bolivia',
            'BRA' => 'Brasil',
            'BHS' => 'The Bahamas',
            'BTN' => 'Druk-Yul',
            'BVT' => 'Bouvet Island',
            'BWA' => 'Botswana',
            'BLR' => 'Беларусь',
            'BLZ' => 'Belize',
            'CAN' => 'Canada',
            'CCK' => 'Cocos (Keeling) Islands',
            'COD' => 'Congo',
            'CAF' => 'Centrafrique',
            'COG' => 'Congo-Brazzaville',
            'CHE' => 'Schweiz',
            'CIV' => 'Côte d’Ivoire',
            'COK' => 'Cook Islands',
            'CHL' => 'Chile',
            'CMR' => 'Cameroun',
            'CHN' => '中华',
            'COL' => 'Colombia',
            'CRI' => 'Costa Rica',
            'CUB' => 'Cuba',
            'CPV' => 'Cabo Verde',
            'CXR' => 'Christmas Island',
            'CYP' => 'Κύπρος / Kıbrıs',
            'CZE' => 'Česko',
            'DEU' => 'Deutschland',
            'DJI' => 'Djibouti',
            'DNK' => 'Danmark',
            'DMA' => 'Dominica',
            'DOM' => 'Quisqueya',
            'DZA' => 'الجزائ',
            'ECU' => 'Ecuador',
            'EST' => 'Eesti',
            'EGY' => 'مصر',
            'ESH' => 'الصحراء الغربي',
            'ERI' => 'ኤርትራ',
            'ESP' => 'España',
            'ETH' => 'ኢትዮጵያ',
            'FIN' => 'Suomi',
            'FJI' => 'Fiji / Viti',
            'FLK' => 'Falkland Islands',
            'FSM' => 'Micronesia',
            'FRO' => 'Føroyar / Færøerne',
            'FRA' => 'France',
            'GAB' => 'Gabon',
            'GBR' => 'United Kingdom',
            'GRD' => 'Grenada',
            'GEO' => 'საქართველო',
            'GUF' => 'Guyane française',
            'GHA' => 'Ghana',
            'GIB' => 'Gibraltar',
            'GRL' => 'Grønland',
            'GMB' => 'Gambia',
            'GIN' => 'Guinée',
            'GLP' => 'Guadeloupe',
            'GNQ' => 'Guinea Ecuatorial',
            'GRC' => 'Ελλάδα',
            'SGS' => 'South Georgia and the South Sandwich Islands',
            'GTM' => 'Guatemala',
            'GUM' => 'Guåhån',
            'GNB' => 'Guiné-Bissau',
            'GUY' => 'Guyana',
            'HKG' => '香港',
            'HND' => 'Honduras',
            'HRV' => 'Hrvatska',
            'HTI' => 'Ayiti',
            'HUN' => 'Magyarország',
            'IDN' => 'Indonesia',
            'IRL' => 'Éire',
            'ISR' => 'ישראל',
            'IND' => 'India',
            'IOT' => 'British Indian Ocean Territory',
            'IRQ' => 'العراق / عيَراق',
            'IRN' => 'ايران',
            'ISL' => 'Ísland',
            'ITA' => 'Italia',
            'JAM' => 'Jamaica',
            'JOR' => 'أردنّ',
            'JPN' => '日本',
            'KEN' => 'Kenya',
            'KGZ' => 'Кыргызстан',
            'KHM' => 'Kâmpŭchea',
            'KIR' => 'Kiribati',
            'COM' => 'اتحاد القمر',
            'KNA' => 'Saint Kitts and Nevis',
            'PRK' => '북조선',
            'KOR' => '한국',
            'KWT' => 'الكويت',
            'CYM' => 'Cayman Islands',
            'KAZ' => 'Қазақстан /Казахстан',
            'LAO' => 'ເມືອງລາວ',
            'LBN' => 'لبنان',
            'LCA' => 'Saint Lucia',
            'LIE' => 'Liechtenstein',
            'LKA' => 'ශ්‍රී ලංකා / இலங்கை',
            'LBR' => 'Liberia',
            'LSO' => 'Lesotho',
            'LTU' => 'Lietuva',
            'LUX' => 'Luxemburg',
            'LVA' => 'Latvija',
            'LBY' => 'ليبيا',
            'MAR' => 'المغربية',
            'MCO' => 'Monaco',
            'MDA' => 'Moldova',
            'MDG' => 'Madagascar',
            'MHL' => 'Marshall Islands',
            'MKD' => 'Македонија',
            'MLI' => 'Mali',
            'MMR' => 'Myanmar',
            'MNG' => 'Монгол Улс',
            'MAC' => '澳門 / Macau',
            'MNP' => 'Northern Marianas',
            'MTQ' => 'Martinique',
            'MRT' => 'الموريتانية',
            'MSR' => 'Montserrat',
            'MLT' => 'Malta',
            'MUS' => 'Mauritius',
            'MDV' => 'ޖުމުހޫރިއްޔ',
            'MWI' => 'Malawi',
            'MEX' => 'México',
            'MYS' => 'مليسيا',
            'MOZ' => 'Moçambique',
            'NAM' => 'Namibia',
            'NCL' => 'Nouvelle-Calédonie',
            'NER' => 'Niger',
            'NFK' => 'Norfolk Island',
            'NGA' => 'Nigeria',
            'NIC' => 'Nicaragua',
            'NLD' => 'Nederland',
            'NOR' => 'Norge',
            'NPL' => 'नेपाल',
            'NRU' => 'Naoero',
            'NIU' => 'Niue',
            'NZL' => 'New Zealand / Aotearoa',
            'OMN' => 'عُمان',
            'PAN' => 'Panamá',
            'PER' => 'Perú',
            'PYF' => 'Polynésie française',
            'PNG' => 'Papua New Guinea  / Papua Niugini',
            'PHL' => 'Philippines',
            'PAK' => 'پاکستان',
            'POL' => 'Polska',
            'SPM' => 'Saint-Pierre-et-Miquelon',
            'PCN' => 'Pitcairn Islands',
            'PRI' => 'Puerto Rico',
            'PRT' => 'Portugal',
            'PLW' => 'Belau / Palau',
            'PRY' => 'Paraguay',
            'QAT' => 'قطر',
            'REU' => 'Réunion',
            'ROU' => 'România',
            'RUS' => 'Росси́я',
            'RWA' => 'Rwanda',
            'SAU' => 'السعودية',
            'SLB' => 'Solomon Islands',
            'SYC' => 'Seychelles',
            'SDN' => 'Sénégal',
            'SWE' => 'Sverige',
            'SGP' => 'Singapore',
            'SHN' => 'Saint Helena, Ascension and Tristan da Cunha',
            'SVN' => 'Slovenija',
            'SJM' => 'Svalbard',
            'SVK' => 'Slovensko',
            'SLE' => 'Sierra Leone',
            'SMR' => 'San Marino',
            'SEN' => 'Sénégal',
            'SOM' => 'Soomaaliya',
            'SUR' => 'Suriname',
            'STP' => 'São Tomé e Príncipe',
            'SLV' => 'El Salvador',
            'SYR' => 'سوري',
            'SWZ' => 'weSwatini',
            'TCA' => 'Turks and Caicos Islands',
            'TCD' => 'Tchad',
            'ATF' => 'Terres australes fran‡aises',
            'TGO' => 'Togo',
            'THA' => 'ไทย',
            'TJK' => 'Тоҷикистон',
            'TKL' => 'Tokelau',
            'TKM' => 'Türkmenistan',
            'TUN' => 'التونسية',
            'TON' => 'Tonga',
            'TLS' => 'Timor Lorosa\'e',
            'TUR' => 'Türkiye',
            'TTO' => 'Trinidad and Tobago',
            'TUV' => 'Tuvalu',
            'TWN' => '中華',
            'TZA' => 'Tanzania',
            'UKR' => 'Україна',
            'UGA' => 'Uganda',
            'UMI' => 'United States Minor Outlying Islands',
            'USA' => 'United States',
            'URY' => 'Uruguay',
            'UZB' => 'O‘zbekiston',
            'VAT' => 'Vaticano',
            'VCT' => 'Saint Vincent and the Grenadines',
            'VEN' => 'Venezuela',
            'VGB' => 'British Virgin Islands',
            'VIR' => 'US Virgin Islands',
            'VNM' => 'Việt Nam',
            'VUT' => 'Vanuatu',
            'WLF' => 'Wallis and Futuna',
            'WSM' => 'Samoa',
            'YEM' => 'اليمنية',
            'MYT' => 'Mayotte',
            'ZAF' => 'Afrika-Borwa',
            'ZMB' => 'Zambia',
            'ZWE' => 'Zimbabwe',
            'PSE' => 'فلسطين',
            'CSG' => 'Србија и Црна Гора',
            'ALA' => 'Åland',
            'HMD' => 'Heard Island and McDonald Islands',
            'MNE' => 'Crna Gora',
            'SRB' => 'Srbija',
            'JEY' => 'Jersey',
            'GGY' => 'Guernsey',
            'IMN' => 'Mann / Mannin',
            'MAF' => 'Saint-Martin',
            'BLM' => 'Saint-Barthélemy',
            'BES' => 'Bonaire, Sint Eustatius en Saba',
            'CUW' => 'Curaçao',
            'SXM' => 'Sint Maarten',
            'SSD' => 'South Sudan',
        ];
        return $countries;
    }
}
