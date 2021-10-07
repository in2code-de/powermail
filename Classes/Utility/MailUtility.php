<?php
declare(strict_types = 1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Extbase\Object\Exception;

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
     * @throws Exception
     */
    public static function sendPlainMail(
        string $receiverEmail,
        string $senderEmail,
        string $subject,
        string $body
    ): bool {
        $message = ObjectUtility::getObjectManager()->get(MailMessage::class);
        $message->setTo([$receiverEmail => '']);
        $message->setFrom([$senderEmail => 'Sender']);
        $message->setSubject($subject);
        $message->text($body);
        $message->send();
        return $message->isSent();
    }
}
