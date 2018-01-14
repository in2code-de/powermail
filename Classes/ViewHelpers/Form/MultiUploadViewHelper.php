<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Form;

use TYPO3\CMS\Fluid\ViewHelpers\Form\UploadViewHelper;

/**
 * Class MultiUploadViewHelper
 */
class MultiUploadViewHelper extends UploadViewHelper
{

    /**
     * Initialize the arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
    }

    /**
     * Renders the upload field.
     *
     * @return string
     */
    public function render()
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
