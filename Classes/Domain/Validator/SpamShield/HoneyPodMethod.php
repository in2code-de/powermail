<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield;

/**
 * Class HoneyPodMethod
 */
class HoneyPodMethod extends AbstractMethod
{

    /**
     * Honeypod Check: Spam recognized if Honeypod field is filled
     *
     * @return bool true if spam recognized
     */
    public function spamCheck()
    {
        return !empty($this->arguments['field']['__hp']);
    }
}
