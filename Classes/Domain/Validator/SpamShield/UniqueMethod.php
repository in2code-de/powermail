<?php
namespace In2code\Powermail\Domain\Validator\SpamShield;

/**
 * Class UniqueMethod
 * @package In2code\Powermail\Domain\Validator\SpamShield
 */
class UniqueMethod extends AbstractMethod
{

    /**
     * Unique Check: Checks if values in given params are different
     *
     * @param int $indication Indication if check fails
     * @return int
     */
    public function spamCheck($indication = 3)
    {
        if ($indication) {
            $answers = [];
            foreach ($this->mail->getAnswers() as $answer) {
                if (!is_array($answer->getValue()) && $answer->getValue()) {
                    $answers[] = $answer->getValue();
                }
            }
            if (count($answers) !== count(array_unique($answers))) {
                return $indication;
            }
        }
        return 0;
    }
}
