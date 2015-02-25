<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use In2code\Powermail\Domain\Model\Field;

/**
 * Array for multiple upload
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class UploadAttributesViewHelper extends AbstractValidationViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * Configuration
	 */
	protected $settings = array();

	/**
	 * Array for multiple upload
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \array $additionalAttributes To add further attributes
	 * @return array
	 */
	public function render(Field $field, $additionalAttributes = array()) {
		$this->addMandatoryAttributes($additionalAttributes, $field);
		if ($field->getMultiselectForField()) {
			$additionalAttributes['multiple'] = 'multiple';
		}
		if (!empty($this->settings['misc']['file']['extension'])) {
			$additionalAttributes['accept'] = $this->getDottedListOfExtensions(
				$this->settings['misc']['file']['extension']
			);
		}
		return $additionalAttributes;
	}

	/**
	 * Get extensions with dot as prefix
	 *      before: jpg,png,gif
	 *      after: .jpg,.png,.gif
	 *
	 * @param string $extensionList
	 * @return string
	 */
	protected function getDottedListOfExtensions($extensionList) {
		$extensions = GeneralUtility::trimExplode(',', $extensionList, TRUE);
		return '.' . implode(',.', $extensions);
	}
}