<?php
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * Class OptinUtility
 *
 * @package In2code\Powermail\Utility
 */
class OptinUtility extends AbstractUtility
{

    /**
     * Check if given Hash is the correct Optin Hash
     *
     * @param string $hash
     * @param Mail $mail
     * @return string
     */
    public static function checkOptinHash($hash, Mail $mail)
    {
        $newHash = self::createHash($mail->getUid() . $mail->getPid() . $mail->getForm()->getUid());
        if (!empty($hash) && $newHash === $hash) {
            return true;
        }
        return false;
    }

    /**
     * Create Hash for Optin Mail
     *
     * @param Mail $mail
     * @return string
     */
    public static function createOptinHash(Mail $mail)
    {
        return self::createHash($mail->getUid() . $mail->getPid() . $mail->getForm()->getUid());
    }

    /**
     * Create Hash from String and TYPO3 Encryption Key
     *
     * @param string $string Any String
     * @return string Hashed String
     */
    protected static function createHash($string)
    {
        $string .= self::getEncryptionKey();
        return GeneralUtility::shortMD5($string);
    }
}
