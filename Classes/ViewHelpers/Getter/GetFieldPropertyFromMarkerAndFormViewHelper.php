<?php
namespace In2code\Powermail\ViewHelpers\Getter;

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use In2code\Powermail\Domain\Model\Form;

/**
 * Class GetFieldPropertyFromMarkerAndFormViewHelper
 *
 * @package In2code\Powermail\ViewHelpers\Getter
 */
class GetFieldPropertyFromMarkerAndFormViewHelper extends AbstractViewHelper {

	/**
	 * fieldRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Get a Property from Field by given Marker and Form
	 *
	 * @param string $marker Field Marker
	 * @param Form $form
	 * @param string $property Field Property
	 * @return string Property
	 */
	public function render($marker, Form $form, $property) {
		$field = $this->fieldRepository->findByMarkerAndForm($marker, $form->getUid());
		if ($field !== NULL) {
			return ObjectAccess::getProperty($field, $property);
		}
		return '';
	}
}