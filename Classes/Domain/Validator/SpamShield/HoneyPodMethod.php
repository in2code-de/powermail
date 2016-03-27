<?php
namespace In2code\Powermail\Domain\Validator\SpamShield;

/**
 * Class HoneyPodMethod
 * @package In2code\Powermail\Domain\Validator\SpamShield
 */
class HoneyPodMethod extends AbstractMethod
{

    /**
     * Honeypod Check: Spam recognized if Honeypod field is filled
     *
     * @param int $indication Indication if check fails
     * @return int
     */
    public function spamCheck($indication = 3)
    {
        if ($indication && !empty($this->arguments['field']['__hp'])) {
            return $indication;
        }
        return 0;
    }
}
