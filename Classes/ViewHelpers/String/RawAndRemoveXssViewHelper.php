<?php
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper combines Raw and RemoveXss Methods
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class RawAndRemoveXssViewHelper extends AbstractViewHelper
{

    /**
     * Disable escaping for TYPO3 7.6
     *
     * @var boolean
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * Disable escaping for TYPO3 8.x
     *
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * Disable escaping for TYPO3 8.x
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * ViewHelper combines Raw and RemoveXss Methods
     *
     * @return string
     */
    public function render()
    {
        $string = $this->renderChildren();
        $string = GeneralUtility::removeXSS($string);

        return $string;
    }
}
