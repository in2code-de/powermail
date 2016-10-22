<?php
namespace In2code\Powermail\Utility;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class StringUtility
 *
 * @package In2code\Powermail\Utility
 */
class StringUtility
{

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
        $characters = implode('', range(0, 9)) . implode('', range('a', 'z'));
        if ($lowerAndUpperCase) {
            $characters .= implode('', range('A', 'Z'));
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

    /**
     * Function br2nl is the opposite of nl2br
     *
     * @param string $content
     * @return string
     */
    public static function br2nl($content)
    {
        $array = [
            '<br >',
            '<br>',
            '<br/>',
            '<br />'
        ];
        return str_replace($array, PHP_EOL, $content);
    }

    /**
     * Count length of a string and respect umlauts and breaks as just one character
     *
     * @param string $string
     * @return int
     */
    public static function getStringLength($string)
    {
        $string = str_replace("\r\n", ' ', $string);
        $length = mb_strlen($string, 'utf-8');
        return $length;
    }

    /**
     * Clean strings like filenames
     *      Only allowed characters are a-z, A-Z, 0-9, -, . others will be substituted
     *      In addition string will be changed to lowercase
     *
     * @param string $string
     * @param string $replace
     * @return string
     */
    public static function cleanString($string, $replace = '_')
    {
        $string = strtolower(trim($string));
        $string = preg_replace('~[^a-z0-9-\.]~', $replace, $string);
        return $string;
    }

    /**
     * Forces an integer list
     *
     * @param string $list
     * @return string
     */
    public static function integerList($list)
    {
        return implode(',', GeneralUtility::intExplode(',', $list));
    }

    /**
     * Get src from image tag
     *      <img src="abc" class="" /> => abc
     * 
     * @param string $tag
     * @return string
     */
    public static function getSrcFromImageTag($tag)
    {
        preg_match('/.*src="(.*)".*/U', $tag, $matches);
        return $matches[1];
    }
}
