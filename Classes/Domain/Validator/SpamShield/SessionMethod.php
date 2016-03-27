<?php
namespace In2code\Powermail\Domain\Validator\SpamShield;

use In2code\Powermail\Utility\SessionUtility;

/**
 * Class SessionMethod
 * @package In2code\Powermail\Domain\Validator\SpamShield
 */
class SessionMethod extends AbstractMethod
{

    /**
     * Session Check: Checks if session was started correct on form delivery
     *
     * @param int $indication Indication if check fails
     * @return int
     */
    public function spamCheck($indication = 3)
    {
        if ($indication) {
            $timeFromSession = SessionUtility::getFormStartFromSession(
                $this->mail->getForm()->getUid(),
                $this->settings
            );
            $referrer = $this->arguments['__referrer']['@action'];
            if ($referrer !== 'optinConfirm' && empty($timeFromSession)) {
                return $indication;
            }
        }
        return 0;
    }
}
