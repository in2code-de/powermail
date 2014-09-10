<?php
namespace In2code\Powermail\ViewHelpers\Getter;

/**
 * Get a Property from In2code\Powermail\Domain\Model\Field with Marker and Form
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class GetFieldPropertyFromMarkerAndFormViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * fieldRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Read Label of a field from given UID
	 *
	 * @param string $marker Field Marker
	 * @param \In2code\Powermail\Domain\Model\Form $form
	 * @param string $property Field Property
	 * @return string Property
	 */
	public function render($marker, \In2code\Powermail\Domain\Model\Form $form, $property) {
		$field = $this->fieldRepository->findByMarkerAndForm($marker, $form->getUid());
		if (method_exists($field, 'get' . ucfirst($property))) {
			return $field->{'get' . ucfirst($property)}();
		}
		return '';
	}
}