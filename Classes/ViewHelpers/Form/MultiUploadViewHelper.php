<?php

declare(strict_types=1);

namespace In2code\Powermail\ViewHelpers\Form;

use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

/**
 * Class MultiUploadViewHelper
 *
 * ToDo: Test, whether this class can be replaced by \TYPO3\CMS\Fluid\ViewHelpers\Form\UploadViewHelper
 */
class MultiUploadViewHelper extends AbstractFormFieldViewHelper
{
    protected $tagName = 'input';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerTagAttribute('disabled', 'string', 'Specifies that the input element should be disabled when the page loads');
        $this->registerTagAttribute('multiple', 'string', 'Specifies that the file input element should allow multiple selection of files');
        $this->registerTagAttribute('accept', 'string', 'Specifies the allowed file extensions to upload via comma-separated list, example ".png,.gif"');
        $this->registerArgument('errorClass', 'string', 'CSS class to set if there are errors for this ViewHelper', false, 'f3-form-error');
        $this->registerUniversalTagAttributes();
    }

    /**
     * Renders the upload field.
     *
     * @return string
     */
    public function render(): string
    {
        $name = $this->getName();
        $allowedFields = ['name', 'type', 'tmp_name', 'error', 'size'];
        foreach ($allowedFields as $fieldName) {
            $this->registerFieldNameForFormTokenGeneration($name . '[' . $fieldName . '][]');
        }
        $this->tag->addAttribute('type', 'file');
        $name .= '[]';
        $this->tag->addAttribute('name', $name);
        $this->setErrorClassAttribute();
        return $this->tag->render();
    }
}
