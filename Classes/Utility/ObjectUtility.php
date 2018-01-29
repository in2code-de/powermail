<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Class ObjectUtility
 */
class ObjectUtility extends AbstractUtility
{

    /**
     * @return TypoScriptFrontendController
     */
    public static function getTyposcriptFrontendController()
    {
        return parent::getTyposcriptFrontendController();
    }

    /**
     * @return ObjectManager
     */
    public static function getObjectManager(): ObjectManager
    {
        return parent::getObjectManager();
    }

    /**
     * @return ContentObjectRenderer
     */
    public static function getContentObject(): ContentObjectRenderer
    {
        return parent::getContentObject();
    }

    /**
     * @return array
     */
    public static function getFilesArray(): array
    {
        return parent::getFilesArray();
    }

    /**
     * @return LanguageService
     */
    public static function getLanguageService()
    {
        return parent::getLanguageService();
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
