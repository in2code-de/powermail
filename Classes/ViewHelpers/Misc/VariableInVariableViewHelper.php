<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * Class VariableInVariableViewHelper
 */
class VariableInVariableViewHelper extends AbstractViewHelper implements ViewHelperInterface
{
    /**
     * Initialize arguments.
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('obj', 'mixed', 'Object', true);
        $this->registerArgument('prop', 'string', 'Property', true);
    }

    /**
     * Solution for {outer.{inner}} call in fluid
     * @return mixed
     * @throws PropertyNotAccessibleException
     */
    public function render()
    {
        $obj = $this->arguments['obj'];
        $prop = $this->arguments['prop'];
        if (is_array($obj) && array_key_exists($prop, $obj)) {
            return $obj[$prop];
        }

        if (is_object($obj)) {
            return ObjectAccess::getProperty($obj, GeneralUtility::underscoredToLowerCamelCase($prop));
        }

        return null;
    }
}
