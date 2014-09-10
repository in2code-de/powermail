<?php
namespace In2code\Powermail\ViewHelpers\Getter;

/**
 * Used in the Backendmodule to get a defined piVar
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class GetPiVarAnswerFieldViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Used in the Backendmodule to get a defined piVar
	 *
	 * @param mixed $field Field (UID)
	 * @param array $piVars Plugin Vars
	 * @return string parsed Variable
	 */
	public function render($field, $piVars) {
		$result = '';
		$fieldUid = 0;
		if (is_a($field, '\In2code\Powermail\Domain\Model\Field')) {
			$fieldUid = $field->getUid();
		} elseif (is_numeric($field)) {
			$fieldUid = $field;
		}
		if (!empty($piVars['filter']['answer'][$fieldUid])) {
			$result = htmlspecialchars($piVars['filter']['answer'][$fieldUid]);
		}

		return $result;
	}
}