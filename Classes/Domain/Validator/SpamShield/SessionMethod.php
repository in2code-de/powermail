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
     * @return bool true if spam recognized
     */
    public function spamCheck()
    {
        $timeFromSession = SessionUtility::getFormStartFromSession(
            $this->mail->getForm()->getUid(),
            $this->settings
        );
        $referrer = $this->arguments['__referrer']['@action'];
        return $referrer !== 'optinConfirm' && empty($timeFromSession);
    }
}
