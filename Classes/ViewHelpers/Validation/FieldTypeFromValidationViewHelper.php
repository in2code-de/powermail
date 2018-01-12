<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;

/**
 * Class FieldTypeFromValidationViewHelper
 */
class FieldTypeFromValidationViewHelper extends AbstractValidationViewHelper
{

    /**
     * InputTypes
     *
     * @var array
     */
    protected $html5InputTypes = [
        1 => 'email',
        2 => 'url',
        3 => 'tel',
        4 => 'number',
        8 => 'range'
    ];

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('field', Field::class, 'Field', true);
    }

    /**
     * @return string
     */
    public function render()
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        if (!$this->isNativeValidationEnabled()) {
            return 'text';
        }
        if (array_key_exists($field->getValidation(), $this->html5InputTypes)) {
            return $this->html5InputTypes[$field->getValidation()];
        }
        return 'text';
    }
}
