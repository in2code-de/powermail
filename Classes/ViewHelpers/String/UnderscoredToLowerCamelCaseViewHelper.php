<?php

declare(strict_types=1);

namespace In2code\Powermail\ViewHelpers\String;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * Class UnderscoredToLowerCamelCaseViewHelper
 */
class UnderscoredToLowerCamelCaseViewHelper extends AbstractViewHelper implements ViewHelperInterface
{
    /**
     * Initialize arguments.
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('val', 'string', 'Value');
    }

    /**
     * Underscored value to lower camel case value (nice_field => niceField)
     */
    public function render(): string
    {
        return GeneralUtility::underscoredToLowerCamelCase($this->arguments['val']);
    }
}
