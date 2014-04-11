<?php

/**
 * View helper check if given value is number or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Condition_IsThereAMailWithStartingLetterViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * View helper check if given value is number or not
	 *
	 * @param object $mails Current Mail Query
	 * @param string $letter Starting Letter to search for
	 * @param int $answerField Field Uid
	 * @return boolean
	 */
	public function render($mails, $letter, $answerField) {
		foreach ($mails as $mail) {
			foreach ($mail->getAnswers() as $answer) {
				if ($answer->getField() == $answerField) {
					$value = $answer->getValue();
					if (strtolower($value[0]) == strtolower($letter)) {
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}
}