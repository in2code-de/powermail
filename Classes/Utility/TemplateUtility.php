<?php

declare(strict_types=1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class TemplateUtility
 * @codeCoverageIgnore
 */
class TemplateUtility
{
    /**
     *  Get absolute paths for templates with fallback
     *     Returns paths from *RootPaths and "hardcoded"
     *     paths pointing to the EXT:powermail-resources.
     */
    public static function getTemplateFolders(string $part = 'template'): array
    {
        $templatePaths = [];
        $extbaseConfig = ObjectUtility::getConfigurationManager()->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'powermail'
        );
        if (!empty($extbaseConfig['view'][$part . 'RootPaths'])) {
            $templatePaths = $extbaseConfig['view'][$part . 'RootPaths'];
            ksort($templatePaths, SORT_NUMERIC);
            $templatePaths = array_values($templatePaths);
        }

        if ($templatePaths === []) {
            $templatePaths[] = 'EXT:powermail/Resources/Private/' . ucfirst($part) . 's/';
        }

        $templatePaths = array_unique($templatePaths);
        $absolutePaths = [];
        foreach ($templatePaths as $templatePath) {
            $absolutePaths[] = StringUtility::addTrailingSlash(GeneralUtility::getFileAbsFileName($templatePath));
        }

        return $absolutePaths;
    }

    /**
     *  Return path and filename for a file or path.
     *  Only the first existing file/path will be returned.
     *  respect *RootPaths
     */
    public static function getTemplatePath(string $pathAndFilename, string $part = 'template'): string
    {
        $matches = self::getTemplatePaths($pathAndFilename, $part);
        return $matches === [] ? '' : end($matches);
    }

    /**
     *  Return path and filename for one or many files/paths.
     *         Only existing files/paths will be returned.
     *         respect *RootPaths
     */
    public static function getTemplatePaths(string $pathAndFilename, string $part = 'template'): array
    {
        $matches = [];
        $absolutePaths = self::getTemplateFolders($part);
        foreach ($absolutePaths as $absolutePath) {
            if (file_exists($absolutePath . $pathAndFilename)) {
                $matches[] = $absolutePath . $pathAndFilename;
            }
        }

        return $matches;
    }

    /**
     * Get a default Standalone view
     */
    public static function getDefaultStandAloneView(
        string $format = 'html'
    ): StandaloneView {
        /** @var StandaloneView $standaloneView */
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->setFormat($format);
        $standaloneView->setLayoutRootPaths(self::getTemplateFolders('layout'));
        $standaloneView->setPartialRootPaths(self::getTemplateFolders('partial'));
        return $standaloneView;
    }

    /**
     * This functions renders the powermail_all Template (e.g. useage in Mails)
     */
    public static function powermailAll(
        Mail $mail,
        string $section = 'web',
        array $settings = [],
        string $type = null
    ): ?string {
        $standaloneView = self::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(self::getTemplatePath('Form/PowermailAll.html'));
        $standaloneView->assignMultiple(
            [
                'mail' => $mail,
                'section' => $section,
                'settings' => $settings,
                'type' => $type,
            ]
        );
        return $standaloneView->render();
    }

    /**
     * Parse String with Fluid View
     */
    public static function fluidParseString(string $string, array $variables = []): string
    {
        if ($string === '' || $string === '0'
            || ConfigurationUtility::isDatabaseConnectionAvailable() === false
            || BackendUtility::isBackendContext()
            || Environment::isCli()
        ) {
            return $string;
        }

        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->setRequest($GLOBALS['TYPO3_REQUEST']);
        $standaloneView->setTemplateSource($string);
        $standaloneView->assignMultiple($variables);
        return $standaloneView->render() ?? '';
    }
}
