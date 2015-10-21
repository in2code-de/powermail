<?php
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Mail;

/**
 * CustomValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class CustomValidator extends StringValidator
{

    /**
     * Custom validation of given Params
     *
     * @param Mail $mail
     * @return bool
     */
    public function isValid($mail)
    {
        $this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__, array($mail, $this));
        return $this->isValidState();
    }

}
