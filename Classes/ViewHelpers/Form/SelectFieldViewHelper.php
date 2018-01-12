<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Form;

use TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper;

/**
 * Class SelectFieldViewHelper
 */
class SelectFieldViewHelper extends SelectViewHelper
{

    /**
     * @var array
     */
    protected $originalOptions = [];

    /**
     * Render the tag.
     *
     * @return string rendered tag.
     * @api
     */
    public function render()
    {
        $this->originalOptions = $this->arguments['options'];
        $this->setOptions();
        return parent::render();
    }

    /**
     * Set options with key and value from $field->getModifiedOptions()
     *        convert:
     *            array(
     *                array(
     *                    'label' => 'Red shoes',
     *                    'value' => 'red',
     *                    'selected' => 0
     *                )
     *            )
     *
     *        to:
     *            array(
     *                'red' => 'Red shoes'
     *            )
     *
     *
     * @return void
     */
    protected function setOptions()
    {
        $optionArray = [];
        foreach ($this->arguments['options'] as $option) {
            $optionArray[$option['value']] = $option['label'];
        }
        $this->arguments['options'] = $optionArray;
    }

    /**
     * Render one option tag
     *
     * @param string $value value attribute of the option tag (will be escaped)
     * @param string $label content of the option tag (will be escaped)
     * @param bool $isSelected specifies wether or not to add selected attribute
     * @return string the rendered option tag
     */
    protected function renderOptionTag($value, $label, $isSelected = false)
    {
        unset($isSelected);
        return parent::renderOptionTag(
            $value,
            $label,
            $this->isSelectedAlternative($this->getOptionFromOriginalOptionsByValue($value))
        );
    }

    /**
     * @param string $value
     * @return array
     */
    protected function getOptionFromOriginalOptionsByValue($value)
    {
        foreach ($this->originalOptions as $option) {
            if ((string) $value === $option['value'] || (string) $value === $option['label']) {
                return $option;
            }
        }
        return [];
    }

    /**
     * Check if option is selected
     *
     * @param array $option Current option
     * @return boolean TRUE if the value marked a s selected; FALSE otherwise
     */
    protected function isSelectedAlternative($option)
    {
        if (is_array($this->getValueAttribute())) {
            return $this->isSelectedAlternativeForArray($option);
        }
        return $this->isSelectedAlternativeForString($option);
    }

    /**
     * @param array $option
     * @return bool
     */
    protected function isSelectedAlternativeForString($option)
    {
        if (($option['selected'] && !$this->getValueAttribute()) ||
            ($this->getValueAttribute() &&
                ($option['value'] === $this->getValueAttribute() || $option['label'] === $this->getValueAttribute()))
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param array $option
     * @return bool
     */
    protected function isSelectedAlternativeForArray($option)
    {
        foreach ($this->getValueAttribute() as $singleValue) {
            if (!empty($singleValue) && ($option['value'] === $singleValue || $option['label'] === $singleValue)) {
                return true;
            }
        }
        return false;
    }
}
