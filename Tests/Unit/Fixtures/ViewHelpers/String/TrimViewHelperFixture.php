<?php
namespace In2code\Powermail\Tests\Unit\Fixtures\ViewHelpers\String;

use In2code\Powermail\ViewHelpers\String\TrimViewHelper;

/**
 * Fixture class for mocking renderChildren
 */
class TrimViewHelperFixture extends TrimViewHelper
{
    /**
     * @var string
     */
    protected $renderChildrenString = 'abcdef';

    /**
     * @return string
     */
    public function renderChildren()
    {
        return $this->renderChildrenString;
    }
}
