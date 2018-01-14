<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Getter;

use In2code\Powermail\Domain\Model\Form;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetFieldsFromFormViewHelper
 */
class GetFieldsFromFormViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('form', Form::class, 'Form', true);
        $this->registerArgument('property', 'string', 'Field property', false, 'title');
        $this->registerArgument('htmlSpecialChars', 'bool', 'htmlSpecialChars', false, true);
        $this->registerArgument('fieldType', 'string', 'Empty=allFieldtypes or $field::FIELD_TYPE_*', false, '');
    }

    /**
     * Get all fields from a form
     *
     * @return array
     */
    public function render(): array
    {
        $fields = [];
        /** @var Form $form */
        $form = $this->arguments['form'];
        foreach ($form->getFields($this->arguments['fieldType']) as $field) {
            $fieldProperty = ObjectAccess::getProperty($field, $this->arguments['property']);
            if ($this->arguments['htmlSpecialChars']) {
                $fieldProperty = htmlspecialchars((string)$fieldProperty);
            }
            $fields[] = $fieldProperty;
        }
        return $fields;
    }
}
