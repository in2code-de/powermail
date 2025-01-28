<?php

declare(strict_types=1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Repository\PageRepository;
use In2code\Powermail\Exception\DeprecatedException;
use Throwable;
use TYPO3\CMS\Backend\Routing\Exception\ResourceNotFoundException;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\Router;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BackendUtility
 */
class BackendUtility
{
    /**
     * Check if backend user is admin
     */
    public static function isBackendAdmin(): bool
    {
        if (self::getBackendUserAuthentication()->user !== null) {
            return self::getBackendUserAuthentication()->user['admin'] === 1;
        }

        return false;
    }

    /**
     * Get property from backend user
     *
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
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Create an URI to edit any record
     *
     * @throws RouteNotFoundException
     */
    public static function createEditUri(string $tableName, int $identifier, bool $addReturnUrl = true): string
    {
        $uriParameters = [
            'edit' => [
                $tableName => [
                    $identifier => 'edit',
                ],
            ],
        ];
        if ($addReturnUrl) {
            $uriParameters['returnUrl'] = self::getReturnUrl();
        }

        return self::getRoute('record_edit', $uriParameters);
    }

    /**
     * Create an URI to add a new record
     *
     * @param int $pageIdentifier where to save the new record
     * @throws RouteNotFoundException
     */
    public static function createNewUri(string $tableName, int $pageIdentifier, bool $addReturnUrl = true): string
    {
        $uriParameters = [
            'edit' => [
                $tableName => [
                    $pageIdentifier => 'new',
                ],
            ],
        ];
        if ($addReturnUrl) {
            $uriParameters['returnUrl'] = self::getReturnUrl();
        }

        return self::getRoute('record_edit', $uriParameters);
    }

    /**
     * Get return URL from current request
     *
     * @throws RouteNotFoundException
     */
    protected static function getReturnUrl(): string
    {
        return self::getRoute(self::getModuleName(), self::getCurrentParameters());
    }

    /**
     * @throws RouteNotFoundException
     */
    public static function getRoute(string $route, array $parameters = []): string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute($route, $parameters);
    }

    /**
     * Get module name or route as fallback
     */
    protected static function getModuleName(): string
    {
        $moduleName = 'record_edit';
        if (
            isset($GLOBALS['TYPO3_REQUEST']->getQueryParams()['route'])
            && $GLOBALS['TYPO3_REQUEST']->getQueryParams()['route'] !== null
        ) {
            $routePath = (string)$GLOBALS['TYPO3_REQUEST']->getQueryParams()['route'];
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
     */
    public static function getCurrentParameters(array $getParameters = []): array
    {
        if ($getParameters === []) {
            $getParameters = $GLOBALS['TYPO3_REQUEST']->getQueryParams();
        }

        $parameters = [];
        $ignoreKeys = [
            'M',
            'moduleToken',
            'route',
            'token',
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
     */
    public static function getPidFromBackendPage(string $returnUrl = ''): int
    {
        if ($returnUrl === '' || $returnUrl === '0') {
            $returnUrl = $GLOBALS['TYPO3_REQUEST']->getParsedBody()['returnUrl'] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()['returnUrl'] ?? null ?: '';
        }

        $urlParts = parse_url($returnUrl);
        $urlParts['query'] ??= '';
        parse_str($urlParts['query'], $queryParts);
        if (array_key_exists('id', $queryParts)) {
            return (int)$queryParts['id'];
        }

        return 0;
    }

    /**
     *  Returns the Page TSconfig for page with id, $id
     *
     * @param int $pid
     * @param array|null $rootLine
     * @param bool $returnPartArray
     * @return array
     * @throws DeprecatedException
     */
    public static function getPagesTSconfig(int $pid, ?array $rootLine = null, bool $returnPartArray = false): array
    {
        if ($rootLine !== null || $returnPartArray) {
            throw new DeprecatedException('arguments not supported any more in powermail', 1578947408);
        }

        $array = [];
        try {
            // @extensionScannerIgnoreLine Seems to be a false positive: getPagesTSconfig() still need 3 params
            $array = BackendUtilityCore::getPagesTSconfig($pid);
        } catch (Throwable $throwable) {
            unset($throwable);
        }

        return $array;
    }

    /**
     * Filter a pid array with only the pages that are allowed to be viewed from the backend user.
     * If the backend user is an admin, show all of course - so ignore this filter.
     */
    public static function filterPagesForAccess(array $pids): array
    {
        if (!self::isBackendAdmin()) {
            $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
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

    public static function isBackendContext(): bool
    {
        return isset($GLOBALS['TYPO3_REQUEST']) && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend();
    }
}
