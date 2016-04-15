<?php
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Signal\SignalTrait;

/**
 * CustomValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
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
