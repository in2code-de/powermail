<?php
namespace In2code\Powermail\Domain\Validator\SpamShield;

/**
 * Class NameMethod
 * @package In2code\Powermail\Domain\Validator\SpamShield
 */
class NameMethod extends AbstractMethod
{

    /**
     * Name Check: Compares first- and lastname (shouldn't be the same)
     *
     * @param int $indication Indication if check fails
     * @return int
     */
    public function spamCheck($indication = 3)
    {
        if ($indication) {
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

            if (!empty($firstname) && $firstname === $lastname) {
                return $indication;
            }
        }
        return 0;
    }
}
