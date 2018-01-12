<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Signal\SignalTrait;

/**
 * Class CustomValidator
 */
class CustomValidator extends StringValidator
{
    use SignalTrait;

    /**
     * Custom validation of given Params
     *
     * @param Mail $mail
     * @return bool
     */
    public function isValid($mail)
    {
        $this->signalDispatch(__CLASS__, __FUNCTION__, [$mail, $this]);
        return $this->isValidState();
    }
}
