<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Mail\MailMessage;

/**
 * Class MailUtility
 * @codeCoverageIgnore
 */
class MailUtility
{

    /**
     * Send a plain mail for simple notifies
     *
     * @param string $receiverEmail Email address to send to
     * @param string $senderEmail Email address from sender
     * @param string $subject Subject line
     * @param string $body Message content
     * @return bool mail was sent?
     */
    public static function sendPlainMail($receiverEmail, $senderEmail, $subject, $body)
    {
        /** @var MailMessage $message */
        $message = ObjectUtility::getObjectManager()->get(MailMessage::class);
        $message->setTo([$receiverEmail => '']);
        $message->setFrom([$senderEmail => 'Sender']);
        $message->setSubject($subject);
        $message->setBody($body);
        $message->send();
        return $message->isSent();
    }
}
