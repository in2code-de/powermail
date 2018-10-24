<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class TemplateUtility
 * @codeCoverageIgnore
 */
class TemplateUtility extends AbstractUtility
{

    /**
     * Get absolute paths for templates with fallback
     *        Returns paths from *RootPaths and "hardcoded"
     *        paths pointing to the EXT:powermail-resources.
     *
     * @param string $part "template", "partial", "layout"
     * @return array
     * @throws InvalidConfigurationTypeException
     */
    public static function getTemplateFolders($part = 'template')
    {
        $templatePaths = [];
        $extbaseConfig = self::getConfigurationManager()->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'powermail'
        );
        if (!empty($extbaseConfig['view'][$part . 'RootPaths'])) {
            $templatePaths = $extbaseConfig['view'][$part . 'RootPaths'];
            $templatePaths = array_values($templatePaths);
        }
        if (empty($templatePaths)) {
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
     * Return path and filename for a file or path.
     *        Only the first existing file/path will be returned.
     *        respect *RootPaths
     *
     * @param string $pathAndFilename e.g. Email/Name.html
     * @param string $part "template", "partial", "layout"
     * @return string Filename/path
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     */
    public static function getTemplatePath($pathAndFilename, $part = 'template')
    {
        $matches = self::getTemplatePaths($pathAndFilename, $part);
        return !empty($matches) ? end($matches) : '';
    }

    /**
     * Return path and filename for one or many files/paths.
     *        Only existing files/paths will be returned.
     *        respect *RootPaths
     *
     * @param string $pathAndFilename Path/filename (Email/Name.html) or path
     * @param string $part "template", "partial", "layout"
     * @return array All existing matches found
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     */
    public static function getTemplatePaths($pathAndFilename, $part = 'template')
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
     *
     * @param string $extensionName
     * @param string $pluginName
     * @param string $format
     * @return StandaloneView
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     */
    public static function getDefaultStandAloneView(
        $extensionName = 'Powermail',
        $pluginName = 'Pi1',
        $format = 'html'
    ) {
        /** @var StandaloneView $standaloneView */
        $standaloneView = self::getObjectManager()->get(StandaloneView::class);
        $standaloneView->getRequest()->setControllerExtensionName($extensionName);
        $standaloneView->getRequest()->setPluginName($pluginName);
        $standaloneView->setFormat($format);
        $standaloneView->setLayoutRootPaths(self::getTemplateFolders('layout'));
        $standaloneView->setPartialRootPaths(self::getTemplateFolders('partial'));
        return $standaloneView;
    }

    /**
     * This functions renders the powermail_all Template (e.g. useage in Mails)
     *
     * @param Mail $mail
     * @param string $section
     * @param array $settings
     * @param null $type
     * @return string
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     */
    public static function powermailAll(Mail $mail, $section = 'web', $settings = [], $type = null)
    {
        $standaloneView = self::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(self::getTemplatePath('Form/PowermailAll.html'));
        $standaloneView->assignMultiple(
            [
                'mail' => $mail,
                'section' => $section,
                'settings' => $settings,
                'type' => $type
            ]
        );
        return $standaloneView->render();
    }

    /**
     * Parse String with Fluid View
     *
     * @param string $string Any string
     * @param array $variables Variables
     * @return string Parsed string
     */
    public static function fluidParseString($string, $variables = [])
    {
        if (empty($string) || ConfigurationUtility::isDatabaseConnectionAvailable() === false
            || BackendUtility::isBackendContext()) {
            return $string;
        }
        /** @var StandaloneView $standaloneView */
        $standaloneView = self::getObjectManager()->get(StandaloneView::class);
        $standaloneView->setTemplateSource($string);
        $standaloneView->assignMultiple($variables);
        return $standaloneView->render();
    }
}
