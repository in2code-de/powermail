<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield;

/**
 * Class NameMethod
 */
class NameMethod extends AbstractMethod
{

    /**
     * Name Check: Compares first- and lastname (shouldn't be the same)
     *
     * @return bool true if spam recognized
     */
    public function spamCheck()
    {
        $firstname = $lastname = '';
        $keysFirstName = [
            'first_name',
            'firstname',
            'vorname'
        ];
        $keysLastName = [
            'last_name',
            'lastname',
            'sur_name',
            'surname',
            'nachname',
            'name'
        ];
        foreach ($this->mail->getAnswers() as $answer) {
            if (is_array($answer->getValue())) {
                continue;
            }
            if (in_array($answer->getField()->getMarker(), $keysFirstName)) {
                $firstname = $answer->getValue();
            }
            if (in_array($answer->getField()->getMarker(), $keysLastName)) {
                $lastname = $answer->getValue();
            }
        }
        return !empty($firstname) && $firstname === $lastname;
    }
}
