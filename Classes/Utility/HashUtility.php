<?php

declare(strict_types=1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Exception\ConfigurationIsMissingException;

/**
 * Class HashUtility
 */
class HashUtility
{
    /**
     * @param string $hash
     * @param Mail $mail
     * @param string $role
     * @return bool
     * @throws \Exception
     */
    public static function isHashValid(string $hash, Mail $mail, string $role = 'optin'): bool
    {
        $newHash = self::createHashFromMail($mail, $role);
        return !empty($hash) && $newHash === $hash;
    }

    /**
     * @param Mail $mail
     * @param string $role
     * @return string
     * @throws \Exception
     */
    public static function getHash(Mail $mail, string $role = 'optin'): string
    {
        return self::createHashFromMail($mail, $role);
    }

    /**
     * Create Hash from Mail properties and TYPO3 Encryption Key
     *
     * @param Mail $mail
     * @param string $role
     * @return string
     * @throws \Exception
     */
    private static function createHashFromMail(Mail $mail, string $role = 'optin'): string
    {
        $string = $mail->getUid() . $mail->getPid() . $mail->getForm()->getUid() . $role . self::getEncryptionKey();
        return self::createHashFromString($string);
    }

    /**
     * @param string $string
     * @return string
     */
    private static function createHashFromString(string $string): string
    {
        return hash('sha256', $string);
    }

    /**
     * Get TYPO3 encryption key
     *
     * @return string
     * @SuppressWarnings(PHPMD.Superglobals)
     * @throws ConfigurationIsMissingException
     */
    protected static function getEncryptionKey(): string
    {
        $confVars = ConfigurationUtility::getTypo3ConfigurationVariables();
        if (empty($confVars['SYS']['encryptionKey'])) {
            throw new ConfigurationIsMissingException(
                'No encryption key found in this TYPO3 installation',
                1514910284796
            );
        }
        return $confVars['SYS']['encryptionKey'];
    }
}
