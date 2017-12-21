<?php
namespace In2code\Powermail\ViewHelpers\Getter;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
     * Get all fields from form
     *
     * @param Form $form
     * @param string $property
     * @param bool $htmlSpecialChars
     * @param string $fieldType Empty=allFieldtypes or $field::FIELD_TYPE_*
     * @return array
     */
    public function render(Form $form, $property = 'title', $htmlSpecialChars = true, $fieldType = '')
    {
        $fields = [];
        foreach ($form->getPages() as $page) {
            foreach ($page->getFields() as $field) {
                if ($this->isCorrectFieldType($field, $fieldType)) {
                    $fieldProperty = ObjectAccess::getProperty($field, $property);
                    if ($htmlSpecialChars) {
                        $fieldProperty = htmlspecialchars($fieldProperty);
                    }
                    $fields[] = $fieldProperty;
                }
            }
        }
        return $fields;
    }

    /**
     * @param Field $field
     * @param $fieldType
     * @return bool
     */
    protected function isCorrectFieldType(Field $field, $fieldType)
    {
        if ($fieldType === '') {
            return true;
        } elseif ($fieldType === $field::FIELD_TYPE_BASIC) {
            return $field->isTypeOf($field::FIELD_TYPE_BASIC);
        } elseif ($fieldType === $field::FIELD_TYPE_ADVANCED) {
            return $field->isTypeOf($field::FIELD_TYPE_ADVANCED);
        } elseif ($fieldType === $field::FIELD_TYPE_EXTPORTABLE) {
            return $field->isTypeOf($field::FIELD_TYPE_EXTPORTABLE);
        }
        return false;
    }
}
