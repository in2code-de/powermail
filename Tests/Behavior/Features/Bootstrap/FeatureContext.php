<?php

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\MinkContext;
use In2code\Powermail\Exception\ElementNotFoundException;

/**
 * Class FeatureContext
 */
class FeatureContext extends MinkContext
{

    /**
     * Wait for X seconds
     *
     * @Given /^I wait "([^"]*)" seconds$/
     *
     * @param string|int $seconds
     * @return void
     */
    public function iWaitSeconds($seconds)
    {
        if ($seconds === 'a few') {
            $seconds = 10;
        }
        sleep($seconds);
    }

    /**
     * Search for this string in html sourcecode
     *
     * @Then /^the sourcecode should contain \'([^\']*)\'$/
     *
     * @param string $html
     * @return void
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function theSourcecodeShouldContain($html)
    {
        $html = str_replace('\n', PHP_EOL, $html);
        $this->assertSession()->responseContains($this->fixStepArgument($html));
    }

    /**
     * Click on any HTML element
     *
     * @When /^(?:|I )click on the element "([^"]*)"$/
     *
     * @param string $locator
     * @return void
     * @throws ElementNotFoundException
     */
    public function iClickOnTheElement($locator)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find('css', $locator);

        if (null === $element) {
            throw new ElementNotFoundException(
                sprintf('Could not evaluate CSS selector: "%s"', $locator),
                1579187286
            );
        }

        $element->click();
    }

    /**
     * Select an iframe by name
     *
     * @Given /^I switch to iframe "([^"]*)"$/
     *
     * @param string $arg1
     * @return void
     */
    public function iSwitchToIframe($arg1 = null)
    {
        $this->getSession()->switchToIFrame($arg1);
    }

    /**
     * Select an iframe by number
     *
     * @Given /^I switch to iframe number ([0-9]+)$/
     *
     * @param int $arg1
     * @return void
     * @throws DriverException
     * @throws UnsupportedDriverActionException
     */
    public function iSwitchToIframeNumber(int $arg1 = 0)
    {
        $this->getSession()->getDriver()->switchToIFrame($arg1);
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with a random value$/
     * @param string $field
     * @return void
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillWithRandomValue($field)
    {
        $value = $this->createRandomString();
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with a random email$/
     * @param string $field
     * @return void
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillWithRandomEmail($field)
    {
        $value = $this->createRandomString() . '@email.org';
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * @When I scroll to top
     *
     * @throws \Exception
     */
    public function scrollToTop()
    {
        $function = <<<JS
(function(){
  var elem = document.querySelector("body");
  elem.scrollIntoView(false);
})()
JS;
    }

    /**
     * @When I scroll :selector into view
     *
     * @param string $selector Allowed selectors: #id, .className, //xpath
     * @throws \Exception
     */
    public function scrollIntoView($selector)
    {
        $locator = substr($selector, 0, 1);

        switch ($locator) {
            case '$': // Query selector
                $selector = substr($selector, 1);
                $function = <<<JS
(function(){
  var elem = document.querySelector("$selector");
  elem.scrollIntoView(false);
})()
JS;
                break;

            case '/': // XPath selector
                $function = <<<JS
(function(){
  var elem = document.evaluate("$selector", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
  elem.scrollIntoView(false);
})()
JS;
                break;

            case '#': // ID selector
                $selector = substr($selector, 1);
                $function = <<<JS
(function(){
  var elem = document.getElementById("$selector");
  elem.scrollIntoView(false);
})()
JS;
                break;

            case '.': // Class selector
                $selector = substr($selector, 1);
                $function = <<<JS
(function(){
  var elem = document.getElementsByClassName("$selector");
  elem[0].scrollIntoView(false);
})()
JS;
                break;

            default:
                throw new \Exception(__METHOD__ . ' Couldn\'t find selector: ' . $selector . ' - Allowed selectors: #id, .className, //xpath');
                break;
        }

        try {
            $this->getSession()->executeScript($function);
        } catch (Exception $e) {
            throw new \Exception(__METHOD__ . ' failed');
        }
    }

    /**
     * createRandomFileName
     *
     * @param int $length
     * @param bool $lowerAndUpperCase
     * @return string
     */
    protected function createRandomString($length = 32, $lowerAndUpperCase = true)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        if ($lowerAndUpperCase) {
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $fileName = '';
        for ($i = 0; $i < $length; $i++) {
            $key = mt_rand(0, strlen($characters) - 1);
            $fileName .= $characters[$key];
        }
        return $fileName;
    }
}
