<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class OptinUtility
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
