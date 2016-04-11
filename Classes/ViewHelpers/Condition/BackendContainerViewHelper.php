<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\ViewHelpers\Be\ContainerViewHelper;

/**
 * Usage of f:be.container with parameter $jQueryNamespace
 * even in older TYPO3 versions.
 * The parameter was introduced in TYPO3 7.6.1
 * Problem: ContainerViewHelper::render() has changed parameters
 * multiple times (type, sorting) from TYPO3 6.2.0 to a current version
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class BackendContainerViewHelper extends ContainerViewHelper
{

    /**
     * @var array
     */
    protected $includeCssFiles = null;

    /**
     * @var array
     */
    protected $includeJsFiles = null;

    /**
     * @var bool
     */
    protected $enableClickMenu = false;

    /**
     * @var bool
     */
    protected $loadExtJs = false;

    /**
     * @var bool
     */
    protected $loadExtJsTheme = false;

    /**
     * @var bool
     */
    protected $enableExtJsDebug = false;

    /**
     * @var bool
     */
    protected $loadJQuery = false;

    /**
     * @var string
     */
    protected $jQueryNamespace = null;

    /**
     * @var string
     */
    protected $pageTitle = '';

    /**
     * @var null
     */
    protected $includeRequireJsModules = null;

    /**
     * Default value for unneeded or deprecated property
     *
     * @var bool
     */
    protected $enableJumpToUrl = false;

    /**
     * Default value for unneeded or deprecated property
     *
     * @var bool
     */
    protected $loadPrototype = false;

    /**
     * Default value for unneeded or deprecated property
     *
     * @var bool
     */
    protected $loadScriptaculous = false;

    /**
     * Default value for unneeded or deprecated property
     *
     * @var string
     */
    protected $scriptaculousModule = '';

    /**
     * Default value for unneeded or deprecated property
     *
     * @var string
     */
    protected $extJsAdapter = '';

    /**
     * Default value for unneeded or deprecated property
     *
     * @var string
     */
    protected $addCssFile = null;

    /**
     * Default value for unneeded or deprecated property
     *
     * @var string
     */
    protected $addJsFile = null;

    /**
     * Default value for unneeded or deprecated property
     *
     * @var string
     */
    protected $addJsInlineLabels = null;

    /**
     * Default value for unneeded or deprecated property
     *
     * @var bool
     */
    protected $includeCsh = false;

    /**
     * Call parent::render() and fill parameters dynamically
     * Number of parameters and their sorting depends on
     * TYPO3 version
     *
     * @param string $pageTitle title tag of the module. Not required by default, as BE modules are shown in a frame
     * @param bool $enableClickMenu If TRUE, loads clickmenu.js required by BE context menus. Defaults to TRUE
     * @param bool $loadExtJs specifies whether to load ExtJS library. Defaults to FALSE
     * @param bool $loadExtJsTheme whether to load ExtJS "grey" theme. Defaults to FALSE
     * @param bool $enableExtJsDebug if TRUE, debug version of ExtJS is loaded. Use this for development only
     * @param bool $loadJQuery whether to load jQuery library. Defaults to FALSE
     * @param array $includeCssFiles List of custom CSS file to be loaded
     * @param array $includeJsFiles List of custom JavaScript file to be loaded
     * @param array $addJsInlineLabels Custom labels to add to JavaScript inline labels
     * @param array $includeRequireJsModules List of RequireJS modules to be loaded
     * @param string $jQueryNamespace Store the jQuery object in a specific namespace
     * @return string
     */
    public function render(
        $pageTitle = '',
        $enableClickMenu = true,
        $loadExtJs = false,
        $loadExtJsTheme = true,
        $enableExtJsDebug = false,
        $loadJQuery = false,
        $includeCssFiles = null,
        $includeJsFiles = null,
        $addJsInlineLabels = null,
        $includeRequireJsModules = null,
        $jQueryNamespace = null
    ) {
        $this->pageTitle = $pageTitle;
        $this->enableClickMenu = filter_var($enableClickMenu, FILTER_VALIDATE_BOOLEAN);
        $this->loadExtJs = filter_var($loadExtJs, FILTER_VALIDATE_BOOLEAN);
        $this->loadExtJsTheme = filter_var($loadExtJsTheme, FILTER_VALIDATE_BOOLEAN);
        $this->enableExtJsDebug = filter_var($enableExtJsDebug, FILTER_VALIDATE_BOOLEAN);
        $this->loadJQuery = filter_var($loadJQuery, FILTER_VALIDATE_BOOLEAN);
        $this->includeCssFiles = $includeCssFiles;
        $this->includeJsFiles = $includeJsFiles;
        $this->addJsInlineLabels = $addJsInlineLabels;
        $this->includeRequireJsModules = $includeRequireJsModules;
        $this->jQueryNamespace = $jQueryNamespace;

        return parent::render(
            $this->getArgumentForKey(0),
            $this->getArgumentForKey(1),
            $this->getArgumentForKey(2),
            $this->getArgumentForKey(3),
            $this->getArgumentForKey(4),
            $this->getArgumentForKey(5),
            $this->getArgumentForKey(6),
            $this->getArgumentForKey(7),
            $this->getArgumentForKey(8),
            $this->getArgumentForKey(9),
            $this->getArgumentForKey(10),
            $this->getArgumentForKey(11),
            $this->getArgumentForKey(12),
            $this->getArgumentForKey(13),
            $this->getArgumentForKey(14),
            $this->getArgumentForKey(15),
            $this->getArgumentForKey(16),
            $this->getArgumentForKey(17)
        );
    }

    /**
     * Return argument property from a given key
     *
     * @param int $key
     * @return mixed
     */
    protected function getArgumentForKey($key = 0)
    {
        $argumentName = $this->getArgumentNameFromKey($key);
        if (property_exists($this, $argumentName)) {
            return $this->$argumentName;
        }
        return null;
    }

    /**
     * Get argument name from argument key
     *
     * @param int $key
     * @return string|null
     */
    protected function getArgumentNameFromKey($key = 0)
    {
        $functionArguments = $this->getFunctionArgumentNames(
            'TYPO3\CMS\Fluid\ViewHelpers\Be\ContainerViewHelper',
            'render'
        );
        if (array_key_exists($key, $functionArguments)) {
            return $functionArguments[$key];
        }
        return null;
    }

    /**
     * Get arguments from a given method name
     *
     * @param string $class
     * @param string $method
     * @return array
     */
    protected function getFunctionArgumentNames($class, $method)
    {
        $function = new \ReflectionMethod($class, $method);
        $result = [];
        foreach ($function->getParameters() as $param) {
            $result[] = $param->name;
        }
        return $result;
    }
}
