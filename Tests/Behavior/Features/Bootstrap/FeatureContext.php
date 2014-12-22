<?php

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException,
	Behat\Behat\Context\Step\Given,
	Behat\Behat\Context\Step\Then,
	Behat\Behat\Context\Step\When,
	Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;

/**
 * Class FeatureContext
 */
class FeatureContext extends \Behat\MinkExtension\Context\MinkContext {

	/**
	 * Wait for X seconds
	 *
	 * @Given /^I wait "([^"]*)" seconds$/
	 *
	 * @param string|int $seconds
	 * @return void
	 */
	public function iWaitSeconds($seconds) {
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
	 */
	public function theSourcecodeShouldContain($html) {
		$html = str_replace('\n', "\n", $html);
		$this->assertSession()->responseContains($this->fixStepArgument($html));
	}

	/**
	 * Click on any HTML element
	 *
	 * @When /^(?:|I )click on the element "([^"]*)"$/
	 *
	 * @param string $locator
	 * @return void
	 */
	public function iClickOnTheElement($locator) {
		$session = $this->getSession();
		$element = $session->getPage()->find('css', $locator);

		if (NULL === $element) {
			throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
		}

		$element->click();
	}

	/**
	 * Select an iframe
	 *
	 * @Given /^I swith to iframe "([^"]*)"$/
	 *
	 * @param string $arg1
	 * @return void
	 */
	public function iSwithToIframe($arg1 = NULL) {
		$this->getSession()->switchToIFrame($arg1);
	}

	/**
	 * Fills in form field with specified id|name|label|value.
	 *
	 * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with a random value$/
	 * @return void
	 */
	public function fillWithRandomValue($field) {
		$value = $this->createRandomString();
		$field = $this->fixStepArgument($field);
		$value = $this->fixStepArgument($value);
		$this->getSession()->getPage()->fillField($field, $value);
	}

	/**
	 * Fills in form field with specified id|name|label|value.
	 *
	 * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with a random email$/
	 * @return void
	 */
	public function fillWithRandomEmail($field) {
		$value = $this->createRandomString() . '@email.org';
		$field = $this->fixStepArgument($field);
		$value = $this->fixStepArgument($value);
		$this->getSession()->getPage()->fillField($field, $value);
	}

	/**
	 * createRandomFileName
	 *
	 * @param int $length
	 * @param bool $lowerAndUpperCase
	 * @return string
	 */
	protected function createRandomString($length = 32, $lowerAndUpperCase = TRUE) {
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