<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * Class VariableInVariableViewHelper
 */
class VariableInVariableViewHelper extends AbstractViewHelper implements ViewHelperInterface
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments.
     *
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('obj', 'mixed', 'Object', true);
        $this->registerArgument('prop', 'string', 'Property', true);
    }

    /**
     * Solution for {outer.{inner}} call in fluid
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $obj = $arguments['obj'];
        $prop = $arguments['prop'];
        if (is_array($obj) && array_key_exists($prop, $obj)) {
            return $obj[$prop];
        }
        if (is_object($obj)) {
            return ObjectAccess::getProperty($obj, GeneralUtility::underscoredToLowerCamelCase($prop));
        }
        return null;
    }
}
