<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * Is {outer.{inner}} a datetime?
 */
class IsDateTimeVariableInVariableViewHelper extends AbstractViewHelper implements ViewHelperInterface
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('obj', 'object', 'Object', true);
        $this->registerArgument('prop', 'string', 'Property', true);
    }

    /**
     * Is {outer.{inner}} a datetime?
     */
    public function render()
    {
        return is_a(
            ObjectAccess::getProperty(
                $this->arguments['obj'],
                GeneralUtility::underscoredToLowerCamelCase($this->arguments['prop'])
            ),
            \DateTime::class
        );
    }
}
