<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * View helper check if given value is number or not
 */
class IsNumberViewHelper extends AbstractViewHelper implements ViewHelperInterface
{
    /**
     * Initialize arguments.
     *
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('val', 'string', 'Value');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     */
    public function render()
    {
        return is_numeric($this->arguments['val']);
    }
}
