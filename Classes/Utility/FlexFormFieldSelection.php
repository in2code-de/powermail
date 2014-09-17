<?php

/**
 * Class FlexFormFieldSelection
 */
class Tx_Powermail_Utility_FlexFormFieldSelection {

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
	 * 			tx_powermail.flexForm.validation.addFieldOptions.newfield = New Validation Name
	 * 			tx_powermail.flexForm.feUserProperty.addFieldOptions.newfield = New fe_user Property
	 *
	 * @param $params
	 * @param $pObj
	 * @return void
	 */
	public function addOptions(&$params, &$pObj) {
		$tSConfig = t3lib_BEfunc::getPagesTSconfig($params['row']['pid']);

		if (!empty($tSConfig[$this->extension . '.']['flexForm.'][$params['config']['itemsProcFuncFieldName'] . '.']['addFieldOptions.'])) {
			$options = $tSConfig[$this->extension . '.']['flexForm.'][$params['config']['itemsProcFuncFieldName'] . '.']['addFieldOptions.'];
			foreach ((array) $options as $value => $label) {
				$params['items'][] = array(
					$label,
					$value
				);
			}
		}
	}
}