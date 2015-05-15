<?php
namespace In2code\Powermail\Utility\Tca;

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class AddOptionsToSelection allows to add individual options
 *
 * @package In2code\Powermail\Utility\Tca
 */
class AddOptionsToSelection {

	/**
	 * Extension prefix
	 *
	 * @var string
	 */
	protected $extension = 'tx_powermail';

	/**
	 * Add options to FlexForm Selection - Options can be defined in TSConfig
	 * 		Use page tsconfig in this way:
	 * 			tx_powermail.flexForm.type.addFieldOptions.newfield = New Field Name
	 *
	 * @param array $params
	 * @return void
	 */
	public function addOptionsForType(&$params) {
		$this->addOptions($params, 'type');
	}

	/**
	 * Add options to FlexForm Selection - Options can be defined in TSConfig
	 * 		Use page tsconfig in this way:
	 * 			tx_powermail.flexForm.validation.addFieldOptions.100 = New Validation
	 *
	 * @param array $params
	 * @return void
	 */
	public function addOptionsForValidation(&$params) {
		$this->addOptions($params, 'validation');
	}

	/**
	 * Add options to FlexForm Selection - Options can be defined in TSConfig
	 * 		Use page tsconfig in this way:
	 * 			tx_powermail.flexForm.feUserProperty.addFieldOptions.newfield = New fe_user
	 *
	 * @param array $params
	 * @return void
	 */
	public function addOptionsForFeUserProperty(&$params) {
		$this->addOptions($params, 'feUserProperty');
	}

	/**
	 * Add options to FlexForm Selection
	 *
	 * @param array $params
	 * @param string $type "type", "validation", "feUserProperty"
	 * @return void
	 */
	protected function addOptions(&$params, $type) {
		$typoScriptConfiguration = BackendUtility::getPagesTSconfig($params['row']['pid']);
		$extensionConfiguration = $typoScriptConfiguration[$this->extension . '.']['flexForm.'];

		if (!empty($extensionConfiguration[$type . '.']['addFieldOptions.'])) {
			$options = $extensionConfiguration[$type . '.']['addFieldOptions.'];
			foreach ((array) $options as $value => $label) {
				if (stristr($value, '.')) {
					continue;
				}
				$params['items'][] = array(
					$label,
					$value
				);
			}
		}
	}

}