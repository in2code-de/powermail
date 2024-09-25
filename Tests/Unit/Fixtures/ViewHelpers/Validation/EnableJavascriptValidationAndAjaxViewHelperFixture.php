<?php

namespace In2code\Powermail\Tests\Unit\Fixtures\ViewHelpers\Validation;

use In2code\Powermail\ViewHelpers\Validation\EnableJavascriptValidationAndAjaxViewHelper;

/**
 * Fixture class for mocking getPagesTSconfig
 */
class EnableJavascriptValidationAndAjaxViewHelperFixture extends EnableJavascriptValidationAndAjaxViewHelper
{
    /**
     * @var string
     */
    protected $redirectUri = 'index.php?id=123';

    /**
     * @return string
     */
    protected function getRedirectUri()
    {
        return $this->redirectUri;
    }
}
