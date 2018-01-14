<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns Error Class if Error in form
 */
class ErrorClassViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('field', Field::class, 'Field', true);
        $this->registerArgument('class', 'string', 'Class name', false, 'error');
    }

    /**
     * @return string
     */
    public function render(): string
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        $validationResults = $this->getRequest()->getOriginalRequestMappingResults();
        $errors = $validationResults->getFlattenedErrors();
        foreach ($errors as $error) {
            /** @var \TYPO3\CMS\Extbase\Error\Error $singleError */
            foreach ((array)$error as $singleError) {
                if ($field->getMarker() === $singleError->getCode()) {
                    return $this->arguments['class'];
                }
            }
        }
        return '';
    }

    /**
     * Shortcut for retrieving the request from the controller context
     *
     * @return \TYPO3\CMS\Extbase\Mvc\Request
     */
    protected function getRequest()
    {
        return $this->renderingContext->getControllerContext()->getRequest();
    }
}
