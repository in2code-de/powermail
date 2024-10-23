<?php

declare(strict_types=1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class ObjectUtility
 */
class ObjectUtility
{
    /**
     * @return TypoScriptFrontendController
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getTyposcriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?? null;
    }

    public static function getContentObject(): ContentObjectRenderer
    {
        return GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getConfigurationManager(): ConfigurationManager
    {
        return GeneralUtility::makeInstance(ConfigurationManager::class);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getFilesArray(): array
    {
        return $_FILES;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    public static function getLogger(string $className): Logger
    {
        return GeneralUtility::makeInstance(LogManager::class)->getLogger($className);
    }
}
