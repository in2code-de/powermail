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
			$seconds = 8;
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
}