<?php
namespace In2code\Powermail\Tests\Fixtures\ViewHelpers\Validation;

use In2code\Powermail\ViewHelpers\Validation\EnableParsleyAndAjaxViewHelper;

/**
 * Fixture class for mocking getPagesTSconfig
 */
class EnableParsleyAndAjaxViewHelperFixture extends EnableParsleyAndAjaxViewHelper
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
