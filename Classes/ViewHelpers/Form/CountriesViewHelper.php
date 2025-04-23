<?php

declare(strict_types=1);

namespace In2code\Powermail\ViewHelpers\Form;

use In2code\Powermail\Domain\Service\CountriesFromStaticInfoTablesService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Country\CountryProvider;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CountriesViewHelper
 */
class CountriesViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly CountryProvider $countryProvider,
    ) {
    }

    public function initializeArguments(): void
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
     * @throws PropertyNotAccessibleException
     */
    public function render(): array
    {
        $countries = $this->getCountriesFromCountryAPI();
        if (
            ExtensionManagementUtility::isLoaded('static_info_tables')
            && (string)($this->templateVariableContainer->getByPath('settings.misc.useStaticInfoTables')) === '1'
        ) {
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
     * Build the country array
     *
     * @return array<string, string>
     */
    protected function getCountriesFromCountryAPI(): array
    {
        $allCountries = $this->countryProvider->getAll();
        $countryArrayForVH = [];
        $languageService = $this->getLanguageService();
        foreach ($allCountries as $country) {
            $countryArrayForVH[$country->getAlpha3IsoCode()] = $languageService->sL($country->getLocalizedNameLabel());
        }
        asort($countryArrayForVH);
        return $countryArrayForVH;
    }

    private function getLanguageService(): LanguageService
    {
        $locale = 'default';
        $request = $this->getRequest();
        if ($request instanceof ServerRequestInterface) {
            /** @var SiteLanguage $language */
            $language = $request->getAttribute('language');
            $locale = $language->getLocale()->getName();
        }
        return GeneralUtility::makeInstance(LanguageServiceFactory::class)->create($locale);
    }

    private function getRequest(): ServerRequestInterface|null
    {
        if ($this->renderingContext->hasAttribute(ServerRequestInterface::class)) {
            return $this->renderingContext->getAttribute(ServerRequestInterface::class);
        }
        return null;
    }
}
