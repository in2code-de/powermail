<?php

declare(strict_types=1);

namespace In2code\Powermail\ViewHelpers\Form;

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Class SelectFieldViewHelper
 */
class SelectFieldViewHelper extends AbstractFormFieldViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'select';

    /**
     * @var mixed
     */
    protected $selectedValue;

    /**
     * @var array
     */
    protected $originalOptions = [];

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('size', 'string', 'Size of input field');
        $this->registerTagAttribute('disabled', 'string', 'Specifies that the input element should be disabled when the page loads');
        $this->registerArgument('options', 'array', 'Associative array with internal IDs as key, and the values are displayed in the select box. Can be combined with or replaced by child f:form.select.* nodes.');
        $this->registerArgument('optionsAfterContent', 'boolean', 'If true, places auto-generated option tags after those rendered in the tag content. If false, automatic options come first.', false, false);
        $this->registerArgument('optionValueField', 'string', 'If specified, will call the appropriate getter on each object to determine the value.');
        $this->registerArgument('optionLabelField', 'string', 'If specified, will call the appropriate getter on each object to determine the label.');
        $this->registerArgument('sortByOptionLabel', 'boolean', 'If true, List will be sorted by label.', false, false);
        $this->registerArgument('selectAllByDefault', 'boolean', 'If specified options are selected if none was set before.', false, false);
        $this->registerArgument('errorClass', 'string', 'CSS class to set if there are errors for this ViewHelper', false, 'f3-form-error');
        $this->registerArgument('prependOptionLabel', 'string', 'If specified, will provide an option at first position with the specified label.');
        $this->registerArgument('prependOptionValue', 'string', 'If specified, will provide an option at first position with the specified value.');
        $this->registerArgument('multiple', 'boolean', 'If set multiple options may be selected.', false, false);
        $this->registerArgument('required', 'boolean', 'If set no empty value is allowed.', false, false);
    }

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
        if ($this->arguments['required']) {
            $this->tag->addAttribute('required', 'required');
        }
        $name = $this->getName();
        if ($this->arguments['multiple']) {
            $this->tag->addAttribute('multiple', 'multiple');
            $name .= '[]';
        }
        $this->tag->addAttribute('name', $name);
        $options = $this->getOptions();

        $viewHelperVariableContainer = $this->renderingContext->getViewHelperVariableContainer();

        $this->addAdditionalIdentityPropertiesIfNeeded();
        $this->setErrorClassAttribute();
        $content = '';

        // register field name for token generation.
        $this->registerFieldNameForFormTokenGeneration($name);
        // in case it is a multi-select, we need to register the field name
        // as often as there are elements in the box
        if ($this->arguments['multiple']) {
            $content .= $this->renderHiddenFieldForEmptyValue();
            // Register the field name additional times as required by the total number of
            // options. Since we already registered it once above, we start the counter at 1
            // instead of 0.
            $optionsCount = count($options);
            for ($i = 1; $i < $optionsCount; $i++) {
                $this->registerFieldNameForFormTokenGeneration($name);
            }
            // save the parent field name so that any child f:form.select.option
            // tag will know to call registerFieldNameForFormTokenGeneration
            // this is the reason why "self::class" is used instead of static::class (no LSB)
            $viewHelperVariableContainer->addOrUpdate(
                self::class,
                'registerFieldNameForFormTokenGeneration',
                $name
            );
        }

        $viewHelperVariableContainer->addOrUpdate(self::class, 'selectedValue', $this->getSelectedValue());
        $prependContent = $this->renderPrependOptionTag();
        $tagContent = $this->renderOptionTags($options);
        $childContent = $this->renderChildren();
        $viewHelperVariableContainer->remove(self::class, 'selectedValue');
        $viewHelperVariableContainer->remove(self::class, 'registerFieldNameForFormTokenGeneration');
        if (isset($this->arguments['optionsAfterContent']) && $this->arguments['optionsAfterContent']) {
            $tagContent = $childContent . $tagContent;
        } else {
            $tagContent .= $childContent;
        }
        $tagContent = $prependContent . $tagContent;

        $this->tag->forceClosingTag(true);
        $this->tag->setContent($tagContent);
        $content .= $this->tag->render();
        return $content;
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
    protected function setOptions(): void
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
        $output = '<option value="' . htmlspecialchars($value) . '"';
        if ($this->isSelectedAlternative($this->getOptionFromOriginalOptionsByValue((string)$value))) {
            $output .= ' selected="selected"';
        }
        $output .= '>' . htmlspecialchars($label) . '</option>';
        return $output;
    }

    /**
     * @param string $value
     * @return array
     */
    protected function getOptionFromOriginalOptionsByValue(string $value): array
    {
        foreach ($this->originalOptions as $option) {
            if ((string)$value === $option['value'] || (string)$value === $option['label']) {
                return $option;
            }
        }
        return [];
    }

    /**
     * Check if option is selected
     *
     * @param array $option Current option
     * @return bool TRUE if the value marked a s selected; FALSE otherwise
     */
    protected function isSelectedAlternative(array $option): bool
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
    protected function isSelectedAlternativeForString(array $option): bool
    {
        if ((!empty($option['selected']) && !$this->getValueAttribute()) ||
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
    protected function isSelectedAlternativeForArray(array $option): bool
    {
        foreach ($this->getValueAttribute() as $singleValue) {
            if (!empty($singleValue) && ($option['value'] === $singleValue || $option['label'] === $singleValue)) {
                return true;
            }
        }
        return false;
    }
    /**
     * Render prepended option tag
     */
    protected function renderPrependOptionTag(): string
    {
        $output = '';
        if ($this->hasArgument('prependOptionLabel')) {
            $value = $this->hasArgument('prependOptionValue') ? $this->arguments['prependOptionValue'] : '';
            $label = $this->arguments['prependOptionLabel'];
            $output .= $this->renderOptionTag((string)$value, (string)$label, false) . LF;
        }
        return $output;
    }

    /**
     * Render the option tags.
     */
    protected function renderOptionTags(array $options): string
    {
        $output = '';
        foreach ($options as $value => $label) {
            $isSelected = $this->isSelected($value);
            $output .= $this->renderOptionTag((string)$value, (string)$label, $isSelected) . LF;
        }
        return $output;
    }

    /**
     * Render the option tags.
     *
     * @return array An associative array of options, key will be the value of the option tag
     */
    protected function getOptions(): array
    {
        if (!is_array($this->arguments['options']) && !$this->arguments['options'] instanceof \Traversable) {
            return [];
        }
        $options = [];
        $optionsArgument = $this->arguments['options'];
        foreach ($optionsArgument as $key => $value) {
            if (is_object($value) || is_array($value)) {
                if ($this->hasArgument('optionValueField')) {
                    $key = ObjectAccess::getPropertyPath($value, $this->arguments['optionValueField']);
                    if (is_object($key)) {
                        if (method_exists($key, '__toString')) {
                            $key = (string)$key;
                        } else {
                            throw new Exception('Identifying value for object of class "' . get_debug_type($value) . '" was an object.', 1247827428);
                        }
                    }
                } elseif ($this->persistenceManager->getIdentifierByObject($value) !== null) {
                    // @todo use $this->persistenceManager->isNewObject() once it is implemented
                    $key = $this->persistenceManager->getIdentifierByObject($value);
                } elseif (is_object($value) && method_exists($value, '__toString')) {
                    $key = (string)$value;
                } elseif (is_object($value)) {
                    throw new Exception('No identifying value for object of class "' . get_class($value) . '" found.', 1247826696);
                }
                if ($this->hasArgument('optionLabelField')) {
                    $value = ObjectAccess::getPropertyPath($value, $this->arguments['optionLabelField']);
                    if (is_object($value)) {
                        if (method_exists($value, '__toString')) {
                            $value = (string)$value;
                        } else {
                            throw new Exception('Label value for object of class "' . get_class($value) . '" was an object without a __toString() method.', 1247827553);
                        }
                    }
                } elseif (is_object($value) && method_exists($value, '__toString')) {
                    $value = (string)$value;
                } elseif ($this->persistenceManager->getIdentifierByObject($value) !== null) {
                    // @todo use $this->persistenceManager->isNewObject() once it is implemented
                    $value = $this->persistenceManager->getIdentifierByObject($value);
                }
            }
            $options[$key] = $value;
        }
        if ($this->arguments['sortByOptionLabel']) {
            asort($options, SORT_LOCALE_STRING);
        }
        return $options;
    }

    /**
     * Render the option tags.
     *
     * @param mixed $value Value to check for
     * @return bool True if the value should be marked as selected.
     */
    protected function isSelected($value): bool
    {
        $selectedValue = $this->getSelectedValue();
        if ($value === $selectedValue || (string)$value === $selectedValue) {
            return true;
        }
        if ($this->hasArgument('multiple')) {
            if ($selectedValue === null && $this->arguments['selectAllByDefault'] === true) {
                return true;
            }
            if (is_array($selectedValue) && in_array($value, $selectedValue)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieves the selected value(s)
     *
     * @return mixed value string or an array of strings
     */
    protected function getSelectedValue()
    {
        $this->setRespectSubmittedDataValue(true);
        $value = $this->getValueAttribute();
        if (!is_array($value) && !$value instanceof \Traversable) {
            return $this->getOptionValueScalar($value);
        }
        $selectedValues = [];
        foreach ($value as $selectedValueElement) {
            $selectedValues[] = $this->getOptionValueScalar($selectedValueElement);
        }
        return $selectedValues;
    }

    /**
     * Get the option value for an object
     *
     * @param mixed $valueElement
     * @return string @todo: Does not always return string ...
     */
    protected function getOptionValueScalar($valueElement)
    {
        if (is_object($valueElement)) {
            if ($this->hasArgument('optionValueField')) {
                return ObjectAccess::getPropertyPath($valueElement, $this->arguments['optionValueField']);
            }
            // @todo use $this->persistenceManager->isNewObject() once it is implemented
            if ($this->persistenceManager->getIdentifierByObject($valueElement) !== null) {
                return $this->persistenceManager->getIdentifierByObject($valueElement);
            }
            return (string)$valueElement;
        }
        return $valueElement;
    }
}
