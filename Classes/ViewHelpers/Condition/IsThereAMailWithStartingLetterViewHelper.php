<?php
namespace In2code\Powermail\ViewHelpers\Condition;

/**
 * Check if there is a mail with a starting letter
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsThereAMailWithStartingLetterViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Check if there is a mail with a starting letter
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $mails
	 * @param \string $letter Starting Letter to search for
	 * @param \int $answerField Field Uid
	 * @return bool
	 */
	public function render($mails, $letter, $answerField) {
		foreach ($mails as $mail) {
			foreach ($mail->getAnswers() as $answer) {
				if (
					method_exists($answer->getField(), 'getUid') &&
					$answer->getField()->getUid() === intval($answerField)
				) {
					$value = $answer->getValue();
					if (strtolower($value[0]) === strtolower($letter)) {
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}
}