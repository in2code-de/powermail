<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield;

/**
 * Class LinkMethod
 */
class LinkMethod extends AbstractMethod
{

    /**
     * Link Check: Counts numbers of links in message
     *
     * @return bool true if spam recognized
     */
    public function spamCheck()
    {
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
        return $linkAmount > (int)$this->configuration['linkLimit'];
    }
}
