<?php
namespace In2code\Powermail\ViewHelpers\Getter;

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
        foreach ($form->getFields($fieldType) as $field) {
            $fieldProperty = ObjectAccess::getProperty($field, $property);
            if ($htmlSpecialChars) {
                $fieldProperty = htmlspecialchars($fieldProperty);
            }
            $fields[] = $fieldProperty;
        }
        return $fields;
    }
}
