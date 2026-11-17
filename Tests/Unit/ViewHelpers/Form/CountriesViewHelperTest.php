<?php

declare(strict_types=1);

namespace In2code\Powermail\Tests\Unit\ViewHelpers\Form;

use In2code\Powermail\ViewHelpers\Form\CountriesViewHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Country\Country;
use TYPO3\CMS\Core\Country\CountryProvider;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

#[CoversClass(CountriesViewHelper::class)]
#[CoversMethod(CountriesViewHelper::class, 'getCountriesFromCountryAPI')]
final class CountriesViewHelperTest extends UnitTestCase
{
    public static function getCountriesFromCountryAPIReturnsSortedCountriesDataProvider(): \Iterator
    {
        yield 'german labels with umlauts' => [
            'de',
            [
                'AT' => ['AUT', 'Österreich'],
                'CY' => ['CYP', 'Zypern'],
                'DE' => ['DEU', 'Deutschland'],
                'EG' => ['EGY', 'Ägypten'],
                'AX' => ['ALA', 'Åland-Inseln'],
                'AF' => ['AFG', 'Afghanistan'],
            ],
            ['AFG', 'EGY', 'ALA', 'DEU', 'AUT', 'CYP'],
        ];
        yield 'english labels' => [
            'en',
            [
                'DE' => ['DEU', 'Germany'],
                'AT' => ['AUT', 'Austria'],
                'CY' => ['CYP', 'Cyprus'],
            ],
            ['AUT', 'CYP', 'DEU'],
        ];
    }

    /**
     * @param array<string, array{0: string, 1: string}> $countries alpha2 => [alpha3, label]
     * @param list<string> $expectedOrder alpha3 codes
     */
    #[DataProvider('getCountriesFromCountryAPIReturnsSortedCountriesDataProvider')]
    #[Test]
    public function getCountriesFromCountryAPIReturnsSortedCountries(
        string $locale,
        array $countries,
        array $expectedOrder
    ): void {
        $countryObjects = [];
        $labels = [];
        foreach ($countries as $alpha2 => [$alpha3, $label]) {
            $country = new Country($alpha2, $alpha3, $label, '0', '', null);
            $countryObjects[] = $country;
            $labels[$country->getLocalizedNameLabel()] = $label;
        }

        $countryProvider = $this->createMock(CountryProvider::class);
        $countryProvider->method('getAll')->willReturn($countryObjects);

        $languageService = $this->createMock(LanguageService::class);
        $languageService->method('sL')->willReturnCallback(static fn (string $key): string => $labels[$key]);
        $languageService->method('getLocale')->willReturn(new Locale($locale));

        $languageServiceFactory = $this->createMock(LanguageServiceFactory::class);
        $languageServiceFactory->method('create')->willReturn($languageService);
        GeneralUtility::addInstance(LanguageServiceFactory::class, $languageServiceFactory);

        /** @var CountriesViewHelper&AccessibleObjectInterface $viewHelper */
        $viewHelper = $this->getAccessibleMock(
            CountriesViewHelper::class,
            null,
            [$countryProvider]
        );
        $viewHelper->setRenderingContext($this->createMock(RenderingContextInterface::class));

        /** @var array<string, string> $sortedCountries */
        $sortedCountries = $viewHelper->_call('getCountriesFromCountryAPI');
        self::assertSame($expectedOrder, array_keys($sortedCountries));
    }
}
