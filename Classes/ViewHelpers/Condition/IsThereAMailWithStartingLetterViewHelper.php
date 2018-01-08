<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Domain\Model\Answer;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsThereAMailWithStartingLetterViewHelper
 */
class IsThereAMailWithStartingLetterViewHelper extends AbstractViewHelper
{

    /**
     * Check if there is a mail with a starting letter
     *
     * @param QueryResult $mails
     * @param string $letter Starting Letter to search for
     * @param int $answerField Field Uid
     * @return bool
     */
    public function render(QueryResult $mails, $letter, $answerField)
    {
        foreach ($mails as $mail) {
            foreach ($mail->getAnswers() as $answer) {
                /** @var Answer $answer */
                if (method_exists($answer->getField(), 'getUid') &&
                    $answer->getField()->getUid() === (int)$answerField
                ) {
                    $value = $answer->getValue();
                    if (strtolower($value[0]) === strtolower($letter)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
