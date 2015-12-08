<?php
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Repository\UserRepository;
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
 * Class StringUtility
 *
 * @package In2code\Powermail\Utility
 */
class StringUtility
{

    /**
     * Get all receiver emails in an array
     *
     * @param string $receiverString String with some emails
     * @param int $feGroup fe_groups Uid
     * @return array
     */
    public static function getReceiverEmails($receiverString, $feGroup)
    {
        $array = self::getEmailsFromString($receiverString);
        if ($feGroup) {
            $array = array_merge($array, self::getEmailsFromFeGroup($feGroup));
        }
        if (ConfigurationUtility::getDevelopmentContextEmail()) {
            $array = [ConfigurationUtility::getDevelopmentContextEmail()];
        }
        return $array;
    }

    /**
     * Read Emails from String
     *
     * @param int $uid fe_groups Uid
     * @return array Array with emails
     */
    protected static function getEmailsFromFeGroup($uid)
    {
        /** @var UserRepository $userRepository */
        $userRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')
            ->get('In2code\\Powermail\\Domain\\Repository\\UserRepository');
        $users = $userRepository->findByUsergroup($uid);
        $array = [];
        foreach ($users as $user) {
            if (GeneralUtility::validEmail($user->getEmail())) {
                $array[] = $user->getEmail();
            }
        }
        return $array;
    }

    /**
     * Read E-Mails from String
     *
     * @param string $string Any given string from a textarea with some emails
     * @return array Array with emails
     */
    protected static function getEmailsFromString($string)
    {
        $array = [];
        $string = str_replace(
            [
                PHP_EOL,
                '|',
                ','
            ],
            ';',
            $string
        );
        $arr = GeneralUtility::trimExplode(';', $string, true);
        foreach ($arr as $email) {
            $array[] = $email;
        }
        return $array;
    }

    /**
     * Check if String/Array is filled
     *
     * @param mixed $value
     * @return bool
     */
    public static function isNotEmpty($value)
    {
        // bool
        if (is_bool($value)) {
            return false;
        }
        // string (default fields)
        if (!is_array($value)) {
            if (isset($value) && strlen($value)) {
                return true;
            }
            // array (checkboxes)
        } else {
            foreach ($value as $subValue) {
                if (isset($value) && strlen($subValue)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * create a random string
     *
     * @param int $length
     * @param bool $lowerAndUpperCase
     * @return string
     */
    public static function getRandomString($length = 32, $lowerAndUpperCase = true)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        if ($lowerAndUpperCase) {
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $fileName = '';
        for ($i = 0; $i < $length; $i++) {
            $key = mt_rand(0, strlen($characters) - 1);
            $fileName .= $characters[$key];
        }
        return $fileName;
    }

    /**
     * Simple function that returns fallback variable
     * if main variable is empty to save unnecessary
     * long if statements
     *
     * @param mixed $variable
     * @param mixed $fallback
     * @return mixed
     */
    public static function conditionalVariable($variable, $fallback)
    {
        if (empty($variable)) {
            return $fallback;
        }
        return $variable;
    }

    /**
     * Check if string starts with another string
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        return stristr($haystack, $needle) && strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * Check if string ends with another string
     *
     * @param string $haystack
     * @param string $needle
     * @return string
     */
    public static function endsWith($haystack, $needle)
    {
        return stristr($haystack, $needle) && strlen($haystack) - strlen($needle) === strpos($haystack, $needle);
    }

    /**
     * Remove last . of a string
     *
     * @param $string
     * @return string
     */
    public static function removeLastDot($string)
    {
        if (substr($string, -1) === '.') {
            $string = substr($string, 0, -1);
        }
        return $string;
    }
}
