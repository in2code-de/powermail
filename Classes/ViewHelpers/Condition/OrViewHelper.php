<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * OrViewHelper
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class OrViewHelper extends AbstractViewHelper
{

    /**
     * OR viewhelper for if widget in fluid
     *
     * @param array $array Array with strings
     * @param string $string String to compare
     * @return boolean
     */
    public function render($array, $string = null)
    {
        foreach ((array)$array as $value) {
            if (!$string && $value) {
                return true;
            }
            if ($string && $value === $string) {
                return true;
            }
        }
        return false;
    }
}
