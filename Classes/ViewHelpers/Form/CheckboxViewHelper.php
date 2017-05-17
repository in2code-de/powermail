<?php
namespace In2code\Powermail\ViewHelpers\Form;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to generate checkbox fields
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class CheckboxViewHelper extends AbstractViewHelper
{

    /**
     * Render the tag.
     *
     * @param object|array $obj object or array
     * @param string $prop property name
     * @param object $field
     * @return mixed
     */
    public function render($obj, $prop, $field)
    {
        $checkboxes = array();
        $searchInput = $obj[$prop];

        if ($settings = $field->getSettings()) {
            $checkboxSetting = explode("\r\n", $settings);
            foreach ($checkboxSetting as $checkbox) {
                $selected = 0;

                $item = explode('|', $checkbox);
                $checkBoxValue = ($item[1] ? $item[1] : $checkbox);

                if (in_array($checkBoxValue, $searchInput)) {
                    $selected = 1;
                }
                $checkboxes[] = array('label' => $item[0], 'value' => $checkBoxValue, 'selected' => $selected);
            }
        }
        return $checkboxes;
    }

}
