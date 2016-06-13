<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Mail\MailMessage;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 in2code.de
 *  Alex Kellner <alexander.kellner@in2code.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class MailUtility
 *
 * @package In2code\Powermail\Utility
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
