<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Shows Content Element
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class ContentElementViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     * @inject
     */
    protected $contentObject;

    /**
     * Parse a content element
     *
     * @param int $uid UID of any content element
     * @return string Parsed Content Element
     */
    public function render($uid)
    {
        $configuration = [
            'tables' => 'tt_content',
            'source' => (int)$uid,
            'dontCheckPid' => 1
        ];
        return $this->contentObject->cObjGetSingle('RECORDS', $configuration);
    }
}
