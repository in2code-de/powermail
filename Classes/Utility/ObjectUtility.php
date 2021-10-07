<?php
declare(strict_types = 1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Object\ObjectManager;
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
        return array_key_exists('TSFE', $GLOBALS) ? $GLOBALS['TSFE'] : null;
    }

    /**
     * @return ObjectManager
     */
    public static function getObjectManager(): ObjectManager
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return ContentObjectRenderer
     * @throws Exception
     */
    public static function getContentObject(): ContentObjectRenderer
    {
        return self::getObjectManager()->get(ContentObjectRenderer::class);
    }

    /**
     * @return ConfigurationManager
     * @codeCoverageIgnore
     * @throws Exception
     */
    public static function getConfigurationManager(): ConfigurationManager
    {
        return self::getObjectManager()->get(ConfigurationManager::class);
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getFilesArray(): array
    {
        return (array)$_FILES;
    }

    /**
     * @return LanguageService
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @param string $className
     * @return Logger
     */
    public static function getLogger(string $className): Logger
    {
        return GeneralUtility::makeInstance(LogManager::class)->getLogger($className);
    }
}
