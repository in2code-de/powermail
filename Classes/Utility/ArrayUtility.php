<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Utility\ArrayUtility as ArrayUtilityCore;

/**
 * Class ArrayUtility
 */
class ArrayUtility extends ArrayUtilityCore
{

    /**
     * Returns array with alphabetical letters
     *
     * @return array
     */
    public static function getAbcArray()
    {
        return range('A', 'Z');
    }

    /**
     * Check if String is JSON Array
     *
     * @param string $string
     * @return bool
     */
    public static function isJsonArray($string)
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
    public static function htmlspecialcharsOnArray($array)
    {
        $newArray = [];
        foreach ((array)$array as $key => $value) {
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
     * @param array|string $path Path within the array
     * @param string $delimiter Defined path delimiter, default .
     * @return mixed
     * @throws \RuntimeException if the path is empty, or if the path does not exist
     * @throws \InvalidArgumentException if the path is neither array nor string
     */
    public static function getValueByPath(array $array, $path, $delimiter = '.')
    {
        try {
            $value = parent::getValueByPath($array, $path, $delimiter);
        } catch (\Exception $exception) {
            // If path is not available in array
            unset($exception);
            $value = '';
        }
        return $value;
    }

    /**
     * Merges two arrays recursively and "binary safe" (integer keys are overridden as well),
     * overruling similar values in the first array ($firstArray) with the values of the second array ($secondArray)
     * In case of identical keys, ie. keeping the values of the second.
     * Originally copied from \TYPO3\CMS\Extbase\Utility\ArrayUtility::arrayMergeRecursiveOverrule in TYPO3 8.7
     *
     * @param array $firstArray First array
     * @param array $secondArray Second array, overruling the first array
     * @param bool $dontAddNewKeys If set, keys that are NOT found in $firstArray (first array) will not be set. Thus only existing value can/will be overruled from second array.
     * @param bool $emptyValuesOverride If set (which is the default), values from $secondArray will overrule if they are empty (according to PHP's empty() function)
     * @return array Resulting array where $secondArray values has overruled $firstArray values
     */
    public static function arrayMergeRecursiveOverrule(
        array $firstArray,
        array $secondArray,
        $dontAddNewKeys = false,
        $emptyValuesOverride = true
    ) {
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
