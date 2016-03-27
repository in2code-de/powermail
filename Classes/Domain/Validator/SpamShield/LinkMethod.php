<?php
namespace In2code\Powermail\Domain\Validator\SpamShield;

/**
 * Class LinkMethod
 * @package In2code\Powermail\Domain\Validator\SpamShield
 */
class LinkMethod extends AbstractMethod
{

    /**
     * Link Check: Counts numbers of links in message
     *
     * @param int $indication Indication if check fails
     * @return int
     */
    public function spamCheck($indication = 3)
    {
        if ($indication) {
            $linkAmount = 0;
            foreach ($this->mail->getAnswers() as $answer) {
                if (is_array($answer->getValue())) {
                    continue;
                }
                preg_match_all('@http://|https://|ftp://@', $answer->getValue(), $result);
                if (isset($result[0])) {
                    $linkAmount += count($result[0]);
                }
            }

            if ($linkAmount > $this->configuration['linkLimit']) {
                return $indication;
            }
        }
        return 0;
    }
}
