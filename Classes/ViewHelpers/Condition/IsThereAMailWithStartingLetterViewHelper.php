<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsThereAMailWithStartingLetterViewHelper
 */
class IsThereAMailWithStartingLetterViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('mails', QueryResult::class, 'Mails', true);
        $this->registerArgument('letter', 'string', 'Starting letter to search for', true);
        $this->registerArgument('answerField', 'int', 'Field uid', true);
    }

    /**
     * Check if there is a mail with a starting letter
     *
     * @return bool
     */
    public function render(): bool
    {
        $mails = $this->arguments['mails'];
        $answerField = $this->arguments['answerField'];
        $letter = $this->arguments['letter'];

        foreach ($mails as $mail) {
            /** @var Mail $mail */
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
