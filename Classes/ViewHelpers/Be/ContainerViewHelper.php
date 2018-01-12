<?php
declare(strict_types=1);

namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\ContainerViewHelper as ContainerViewHelperFluid;

/**
 * Class ContainerViewHelper
 */
class ContainerViewHelper extends ContainerViewHelperFluid
{

    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->allowOutdatedArgumentsInTypo39();
    }

    /**
     * Because we need a container viewhelper that will do its job in TYPO3 8 and 9, we have to add some unneeded
     * attributes for TYPO3 9
     *
     * @return void
     */
    protected function allowOutdatedArgumentsInTypo39()
    {
        if (ConfigurationUtility::isTypo3OlderThen9() === false) {
            $this->registerArgument(
                'enableClickMenu',
                'bool',
                'If TRUE, loads clickmenu.js required by BE context menus. Defaults to TRUE.',
                false,
                true
            );
            $this->registerArgument(
                'loadExtJs',
                'bool',
                'Specifies whether to load ExtJS library. Defaults to FALSE.',
                false,
                false
            );
            $this->registerArgument(
                'loadExtJsTheme',
                'bool',
                'Whether to load ExtJS "grey" theme. Defaults to FALSE.',
                false,
                true
            );
            $this->registerArgument(
                'enableExtJsDebug',
                'bool',
                'If TRUE, debug version of ExtJS is loaded. Use this for development only.',
                false,
                false
            );
            $this->registerArgument(
                'loadJQuery',
                'bool',
                'Whether to load jQuery library. Defaults to FALSE.',
                false,
                false
            );
            $this->registerArgument(
                'jQueryNamespace',
                'string',
                'Store the jQuery object in a specific namespace.'
            );
        }
    }
}
