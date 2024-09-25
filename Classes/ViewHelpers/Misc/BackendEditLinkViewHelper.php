<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Utility\BackendUtility;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class BackendEditLinkViewHelper
 */
class BackendEditLinkViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('tableName', 'string', 'tableName', true);
        $this->registerArgument('identifier', 'int', 'identifier', true);
        $this->registerArgument('addReturnUrl', 'bool', 'addReturnUrl', false, true);
    }

    /**
     * @return string
     * @throws RouteNotFoundException
     */
    public function render(): string
    {
        return BackendUtility::createEditUri(
            $this->arguments['tableName'],
            (int)$this->arguments['identifier'],
            $this->arguments['addReturnUrl']
        );
    }
}
