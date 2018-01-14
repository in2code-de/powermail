<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

/**
 * Class MathematicUtility
 */
class MathematicUtility
{
    /**
     * Basic mathematic operation with two variables and one operator
     *
     * @param int $number1
     * @param int $number2
     * @param string $operator +|-|x|:
     * @return int
     */
    public static function mathematicOperation($number1, $number2, $operator = '+')
    {
        switch ($operator) {
            case '-':
                $result = $number1 - $number2;
                break;
            case 'x':
                $result = $number1 * $number2;
                break;
            case ':':
                $result = $number1 / $number2;
                break;
            case '+':
            default:
                $result = $number1 + $number2;
        }
        return $result;
    }
}
