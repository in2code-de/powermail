<?php
namespace In2code\Powermail\Utility\Tca;

use \In2code\Powermail\Utility\Div,
	\TYPO3\CMS\Backend\Utility\BackendUtility,
	TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AddOptionsToSelection allows to add individual options
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
	 * @param object $pObj
	 * @return void
	 */
	public function addOptionsForType(&$params, $pObj) {
		$this->addOptions($params, 'type');
	}

	/**
	 * Add options to FlexForm Selection - Options can be defined in TSConfig
	 * 		Use page tsconfig in this way:
	 * 			tx_powermail.flexForm.validation.addFieldOptions.100 = New Validation
	 *
	 * @param array $params
	 * @param object $pObj
	 * @return void
	 */
	public function addOptionsForValidation(&$params, $pObj) {
		$this->addOptions($params, 'validation');
	}

	/**
	 * Add options to FlexForm Selection - Options can be defined in TSConfig
	 * 		Use page tsconfig in this way:
	 * 			tx_powermail.flexForm.feUserProperty.addFieldOptions.newfield = New fe_user
	 *
	 * @param array $params
	 * @param object $pObj
	 * @return void
	 */
	public function addOptionsForFeUserProperty(&$params, $pObj) {
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
				$params['items'][] = array(
					$label,
					$value
				);
			}
		}
	}

}