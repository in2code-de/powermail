<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Object\Exception;
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
     * @throws Exception
     */
    public function render(): string
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        $validationResults = $this->getRequest()->getOriginalRequestMappingResults();
        $errors = $validationResults->getFlattenedErrors();
        foreach ($errors as $error) {
            /** @var Error $singleError */
            foreach ((array)$error as $singleError) {
                if (!empty($singleError->getArguments()['marker'])
                    && $field->getMarker() === $singleError->getArguments()['marker']) {
                    return $this->arguments['class'];
                }
            }
        }
        return '';
    }

    /**
     * Shortcut for retrieving the request from the controller context
     *
     * @return Request
     */
    protected function getRequest()
    {
        return $this->renderingContext->getControllerContext()->getRequest();
    }
}
