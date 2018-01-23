<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
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
    protected static function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return TypoScriptFrontendController
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getTyposcriptFrontendController()
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
     */
    protected static function getExtensionConfiguration(): array
    {
        $configuration = [];
        if (ConfigurationUtility::isTypo3OlderThen9()) {
            $configVariables = self::getTypo3ConfigurationVariables();
            // @extensionScannerIgnoreLine We still need to access extConf for TYPO3 8.7
            $possibleConfig = unserialize((string)$configVariables['EXT']['extConf']['powermail']);
            if (!empty($possibleConfig) && is_array($possibleConfig)) {
                $configuration = $possibleConfig;
            }
        } else {
            // @codeCoverageIgnoreStart
            $configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('powermail');
            // @codeCoverageIgnoreEnd
        }
        return $configuration;
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
     * @throws \Exception
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getEncryptionKey(): string
    {
        $confVars = self::getTypo3ConfigurationVariables();
        if (empty($confVars['SYS']['encryptionKey'])) {
            throw new \DomainException('No encryption key found in this TYPO3 installation', 1514910284796);
        }
        return $confVars['SYS']['encryptionKey'];
    }

    /**
     * @return ContentObjectRenderer
     * @codeCoverageIgnore
     */
    protected static function getContentObject(): ContentObjectRenderer
    {
        return self::getObjectManager()->get(ContentObjectRenderer::class);
    }

    /**
     * @return ConfigurationManager
     * @codeCoverageIgnore
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
    protected static function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
