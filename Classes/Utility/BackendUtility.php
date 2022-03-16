<?php
declare(strict_types = 1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Repository\PageRepository;
use In2code\Powermail\Exception\DeprecatedException;
use TYPO3\CMS\Backend\Routing\Exception\ResourceNotFoundException;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\Router;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class BackendUtility
 */
class BackendUtility
{

    /**
     * Check if backend user is admin
     *
     * @return bool
     */
    public static function isBackendAdmin(): bool
    {
        if (isset(self::getBackendUserAuthentication()->user)) {
            return self::getBackendUserAuthentication()->user['admin'] === 1;
        }
        return false;
    }

    /**
     * Get property from backend user
     *
     * @param string|int $property
     * @return string
     */
    public static function getPropertyFromBackendUser(string $property = 'uid')
    {
        if (!empty(self::getBackendUserAuthentication()->user[$property])) {
            return self::getBackendUserAuthentication()->user[$property];
        }
        return '';
    }

    /**
     * @return BackendUserAuthentication
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Create an URI to edit any record
     *
     * @param string $tableName
     * @param int $identifier
     * @param bool $addReturnUrl
     * @return string
     * @throws RouteNotFoundException
     */
    public static function createEditUri(string $tableName, int $identifier, bool $addReturnUrl = true): string
    {
        $uriParameters = [
            'edit' => [
                $tableName => [
                    $identifier => 'edit'
                ]
            ]
        ];
        if ($addReturnUrl) {
            $uriParameters['returnUrl'] = self::getReturnUrl();
        }
        return self::getRoute('record_edit', $uriParameters);
    }

    /**
     * Create an URI to add a new record
     *
     * @param string $tableName
     * @param int $pageIdentifier where to save the new record
     * @param bool $addReturnUrl
     * @return string
     * @throws RouteNotFoundException
     */
    public static function createNewUri(string $tableName, int $pageIdentifier, bool $addReturnUrl = true): string
    {
        $uriParameters = [
            'edit' => [
                $tableName => [
                    $pageIdentifier => 'new'
                ]
            ]
        ];
        if ($addReturnUrl) {
            $uriParameters['returnUrl'] = self::getReturnUrl();
        }
        return self::getRoute('record_edit', $uriParameters);
    }

    /**
     * Get return URL from current request
     *
     * @return string
     * @throws RouteNotFoundException
     */
    protected static function getReturnUrl(): string
    {
        return self::getRoute(self::getModuleName(), self::getCurrentParameters());
    }

    /**
     * @param string $route
     * @param array $parameters
     * @return string
     * @throws RouteNotFoundException
     */
    public static function getRoute(string $route, array $parameters = []): string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute($route, $parameters);
    }

    /**
     * Get module name or route as fallback
     *
     * @return string
     */
    protected static function getModuleName(): string
    {
        $moduleName = 'record_edit';
        if (GeneralUtility::_GET('route') !== null) {
            $routePath = (string)GeneralUtility::_GET('route');
            $router = GeneralUtility::makeInstance(Router::class);
            try {
                $route = $router->match($routePath);
                $moduleName = $route->getOption('_identifier');
            } catch (ResourceNotFoundException $exception) {
                unset($exception);
            }
        }
        return $moduleName;
    }

    /**
     * Get all GET/POST params without module name and token
     *
     * @param array $getParameters
     * @return array
     */
    public static function getCurrentParameters(array $getParameters = []): array
    {
        if (empty($getParameters)) {
            $getParameters = GeneralUtility::_GET();
        }
        $parameters = [];
        $ignoreKeys = [
            'M',
            'moduleToken',
            'route',
            'token'
        ];
        foreach ($getParameters as $key => $value) {
            if (in_array($key, $ignoreKeys)) {
                continue;
            }
            $parameters[$key] = $value;
        }
        return $parameters;
    }

    /**
     * Read pid from returnUrl
     *        URL example:
     *        http://powermail.localhost.de/typo3/alt_doc.php?&
     *        returnUrl=%2Ftypo3%2Fsysext%2Fcms%2Flayout%2Fdb_layout.php%3Fid%3D17%23
     *        element-tt_content-14&edit[tt_content][14]=edit
     *
     * @param string $returnUrl normally used for testing
     * @return int
     */
    public static function getPidFromBackendPage(string $returnUrl = ''): int
    {
        if (empty($returnUrl)) {
            $returnUrl = GeneralUtility::_GP('returnUrl') ?: '';
        }
        $urlParts = parse_url($returnUrl);
        parse_str((string)$urlParts['query'], $queryParts);
        if (array_key_exists('id', $queryParts)) {
            return (int)$queryParts['id'];
        }
        return 0;
    }

    /**
     * Returns the Page TSconfig for page with id, $id
     *
     * @param int $pid
     * @param array $rootLine
     * @param bool $returnPartArray
     * @return array Page TSconfig
     * @throws DeprecatedException
     */
    public static function getPagesTSconfig(int $pid, array $rootLine = null, bool $returnPartArray = false): array
    {
        if ($rootLine !== null || $returnPartArray === true) {
            throw new DeprecatedException('arguments not supported any more in powermail', 1578947408);
        }
        $array = [];
        try {
            // @extensionScannerIgnoreLine Seems to be a false positive: getPagesTSconfig() still need 3 params
            $array = BackendUtilityCore::getPagesTSconfig($pid);
        } catch (\Exception $exception) {
            unset($exception);
        }
        return $array;
    }

    /**
     * Filter a pid array with only the pages that are allowed to be viewed from the backend user.
     * If the backend user is an admin, show all of course - so ignore this filter.
     *
     * @param array $pids
     * @return array
     * @throws Exception
     */
    public static function filterPagesForAccess(array $pids): array
    {
        if (!self::isBackendAdmin()) {
            $pageRepository = ObjectUtility::getObjectManager()->get(PageRepository::class);
            // @codeCoverageIgnoreStart
            $newPids = [];
            foreach ($pids as $pid) {
                $properties = $pageRepository->getPropertiesFromUid((int)$pid);
                if (self::getBackendUserAuthentication()->doesUserHaveAccess($properties, 1)) {
                    $newPids[] = $pid;
                }
            }
            $pids = $newPids;
            // @codeCoverageIgnoreEnd
        }
        return $pids;
    }

    /**
     * @return bool
     */
    public static function isBackendContext(): bool
    {
        return ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend();
    }
}
