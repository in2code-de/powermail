<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * Is {outer.{inner}} a datetime?
 */
class IsDateTimeVariableInVariableViewHelper extends AbstractViewHelper implements ViewHelperInterface
{
    use CompileWithRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('obj', 'object', 'Object', true);
        $this->registerArgument('prop', 'string', 'Property', true);
    }

    /**
     * Is {outer.{inner}} a datetime?
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        return is_a(
            ObjectAccess::getProperty(
                $arguments['obj'],
                GeneralUtility::underscoredToLowerCamelCase($arguments['prop'])
            ),
            \DateTime::class
        );
    }
}
