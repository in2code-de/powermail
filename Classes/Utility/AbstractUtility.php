<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Exception\ConfigurationIsMissingException;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Class AbstractUtility
 */
abstract class AbstractUtility
{

    /**
     * @return BackendUserAuthentication
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return TypoScriptFrontendController
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getTyposcriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getFilesArray(): array
    {
        return (array)$_FILES;
    }

    /**
     * Get extension configuration from LocalConfiguration.php
     *
     * @return array
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected static function getExtensionConfiguration(): array
    {
        return (array)GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('powermail');
    }

    /**
     * Get extension configuration from LocalConfiguration.php
     *
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getTypo3ConfigurationVariables(): array
    {
        return (array)$GLOBALS['TYPO3_CONF_VARS'];
    }

    /**
     * Get TYPO3 encryption key
     *
     * @return string
     * @SuppressWarnings(PHPMD.Superglobals)
     * @throws ConfigurationIsMissingException
     */
    protected static function getEncryptionKey(): string
    {
        $confVars = self::getTypo3ConfigurationVariables();
        if (empty($confVars['SYS']['encryptionKey'])) {
            throw new ConfigurationIsMissingException(
                'No encryption key found in this TYPO3 installation',
                1514910284796
            );
        }
        return $confVars['SYS']['encryptionKey'];
    }

    /**
     * @return ContentObjectRenderer
     * @codeCoverageIgnore
     * @throws Exception
     */
    protected static function getContentObject(): ContentObjectRenderer
    {
        return self::getObjectManager()->get(ContentObjectRenderer::class);
    }

    /**
     * @return ConfigurationManager
     * @codeCoverageIgnore
     * @throws Exception
     */
    protected static function getConfigurationManager(): ConfigurationManager
    {
        return self::getObjectManager()->get(ConfigurationManager::class);
    }

    /**
     * @return ObjectManager
     */
    protected static function getObjectManager(): ObjectManager
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return LanguageService
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
