<?php
namespace In2code\Powermail\ViewHelpers\Getter;

/**
 * Read Marker of a field from given UID
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class GetFieldMarkerFromUidViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

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
	 * @param int $uid
	 * @return string Label
	 */
	public function render($uid) {
		$result = '';
		$field = $this->fieldRepository->findByUid($uid);
		if (method_exists($field, 'getMarker')) {
			$result = $field->getMarker();
		}

		return $result;
	}

}