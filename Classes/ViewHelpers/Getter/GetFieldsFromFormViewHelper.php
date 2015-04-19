<?php
namespace In2code\Powermail\ViewHelpers\Getter;

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use In2code\Powermail\Domain\Model\Form;

/**
 * Get all field from form
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class GetFieldsFromFormViewHelper extends AbstractViewHelper {

	/**
	 * Get all fields from form
	 *
	 * @param Form $form
	 * @param string $property
	 * @param bool $htmlSpecialChars
	 * @return array
	 */
	public function render(Form $form, $property = 'title', $htmlSpecialChars = TRUE) {
		$fields = array();
		foreach ($form->getPages() as $page) {
			foreach ($page->getFields() as $field) {
				$fieldProperty = ObjectAccess::getProperty($field, $property);
				if ($htmlSpecialChars) {
					$fieldProperty = htmlspecialchars($fieldProperty);
				}
				$fields[] = $fieldProperty;
			}
		}
		return $fields;
	}

}