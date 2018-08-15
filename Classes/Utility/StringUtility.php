<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class StringUtility
 */
class StringUtility
{

    /**
     * Check if String/Array is filled
     *
     * @param mixed $value
     * @return bool
     */
    public static function isNotEmpty($value): bool
    {
        // bool
        if (is_bool($value)) {
            return false;
        }
        // string (default fields)
        if (!is_array($value)) {
            if (isset($value) && strlen((string)$value)) {
                return true;
            }
            // array (checkboxes)
        } else {
            foreach ($value as $subValue) {
                if (isset($value) && strlen((string)$subValue)) {
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
    public static function startsWith(string $haystack, string $needle): bool
    {
        return stristr($haystack, $needle) && strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * Check if string ends with another string
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle): bool
    {
        return stristr($haystack, $needle) !== false && substr($haystack, (strlen($needle) * -1)) === $needle;
    }

    /**
     * Remove last . of a string
     *
     * @param $string
     * @return string
     */
    public static function removeLastDot(string $string): string
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
    public static function br2nl(string $content): string
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
    public static function getStringLength($string): int
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
    public static function cleanString(string $string, string $replace = '_'): string
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
    public static function integerList(string $list): string
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
    public static function getSrcFromImageTag(string $tag): string
    {
        preg_match('/.*src="(.*)".*/U', $tag, $matches);
        return $matches[1];
    }

    /**
     * Add a trailing slash to a string (e.g. path)
     *        folder1/folder2 => folder1/folder2/
     *        folder1/folder2/ => folder1/folder2/
     *
     * @param string $string
     * @return string
     */
    public static function addTrailingSlash(string $string): string
    {
        return rtrim($string, '/') . '/';
    }
}
