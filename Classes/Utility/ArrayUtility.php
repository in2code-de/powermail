<?php
declare(strict_types = 1);
namespace In2code\Powermail\Utility;

use Throwable;
use TYPO3\CMS\Core\Utility\ArrayUtility as ArrayUtilityCore;

/**
 * Class ArrayUtility
 */
class ArrayUtility
{
    /**
     * Returns array with alphabetical letters
     *
     * @return array
     */
    public static function getAbcArray(): array
    {
        return range('A', 'Z');
    }

    /**
     * Check if String is JSON Array
     *
     * @param string $string
     * @return bool
     */
    public static function isJsonArray(string $string): bool
    {
        if (!is_string($string)) {
            return false;
        }
        return is_array(json_decode($string, true));
    }

    /**
     * Use htmlspecialchars on array (key and value) (any depth - recursive call)
     *
     * @param array $array Any array
     * @return array Cleaned array
     */
    public static function htmlspecialcharsOnArray(array $array): array
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $newArray[htmlspecialchars((string)$key)] = self::htmlspecialcharsOnArray($value);
            } else {
                $newArray[htmlspecialchars((string)$key)] = htmlspecialchars((string)$value);
            }
        }
        unset($array);
        return $newArray;
    }

    /**
     * Returns a value by given path
     * Overrule delimiter with a "." instead of "/"
     *
     * @param array $array Input array
     * @param string $path Path within the array
     * @param string $delimiter Defined path delimiter, default .
     * @return mixed
     */
    public static function getValueByPath(array $array, string $path, string $delimiter = '.')
    {
        try {
            $value = ArrayUtilityCore::getValueByPath($array, $path, $delimiter);
        } catch (Throwable $exception) {
            // If path is not available in array
            unset($exception);
            $value = '';
        }
        return $value;
    }

    /**
     * Flatten an multidimensional array by key
     * [
     *  [
     *      'title' => 'abc,
     *      'uid' => 1
     *  ],
     *  [
     *      'title' => 'def
     *  ]
     * ]
     *
     * =>
     *
     * [
     *  'abc',
     *  'def'
     * ]
     *
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function flatten(array $array, string $key): array
    {
        $result = [];
        foreach ($array as $sub) {
            if (array_key_exists($key, $sub)) {
                $result[] = $sub[$key];
            }
        }
        return $result;
    }

    /**
     * Merges two arrays recursively and "binary safe" (integer keys are overridden as well),
     * overruling similar values in the first array ($firstArray) with the values of the second array ($secondArray)
     * In case of identical keys, ie. keeping the values of the second.
     * Originally copied from \TYPO3\CMS\Extbase\Utility\ArrayUtility::arrayMergeRecursiveOverrule in TYPO3 8.7
     *
     * @param array $firstArray First array
     * @param array $secondArray Second array, overruling the first array
     * @param bool $dontAddNewKeys If set, keys that are NOT found in $firstArray (first array) will not be set.
     * @param bool $emptyValuesOverride If set, values from $secondArray will overrule if they are empty.
     * @return array Resulting array where $secondArray values has overruled $firstArray values
     */
    public static function arrayMergeRecursiveOverrule(
        array $firstArray,
        array $secondArray,
        bool $dontAddNewKeys = false,
        bool $emptyValuesOverride = true
    ): array {
        foreach ($secondArray as $key => $value) {
            if (array_key_exists($key, $firstArray) && is_array($firstArray[$key])) {
                if (is_array($secondArray[$key])) {
                    $firstArray[$key] = self::arrayMergeRecursiveOverrule(
                        $firstArray[$key],
                        $secondArray[$key],
                        $dontAddNewKeys,
                        $emptyValuesOverride
                    );
                } else {
                    $firstArray[$key] = $secondArray[$key];
                }
            } else {
                if ($dontAddNewKeys) {
                    // @codeCoverageIgnoreStart
                    if (array_key_exists($key, $firstArray)) {
                        if ($emptyValuesOverride || !empty($value)) {
                            $firstArray[$key] = $value;
                        }
                    }
                    // @codeCoverageIgnoreEnd
                } else {
                    if ($emptyValuesOverride || !empty($value)) {
                        $firstArray[$key] = $value;
                    }
                }
            }
        }
        reset($firstArray);
        return $firstArray;
    }
}
