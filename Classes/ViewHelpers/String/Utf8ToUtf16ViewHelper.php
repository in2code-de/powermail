<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class Utf8ToUtf16ViewHelper
 */
class Utf8ToUtf16ViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * change utf8 to UTF-16LE (normally for Excel export files)
     *
     * @return string
     */
    public function render(): string
    {
        $string = chr(255) . chr(254);
        $string .= mb_convert_encoding($this->renderChildren(), 'UTF-16LE', 'UTF-8');
        return $string;
    }
}
