# Features/Pi1/Validation/PhpValidation.feature
@Pi1ValidationPhpValidation
Feature: PhpValidation
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # Form is rendered?
  Scenario: Searching for a ValidationForm
    Given I am on "/index.php?id=23"
    Then I should see "Input"
    Then I should see "Input (Pattern http://ww)"
    Then I should see "Textarea"
    Then I should see "Select"
    Then I should see "Select Multi"
    Then I should see "Check"
    Then I should see "Radio"
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I should see 7 ".powermail_message_error > li" elements
    Then I fill in "tx_powermail_pi1[field][lastname]" with "Christian"
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I should see 6 ".powermail_message_error > li" elements
    Then I fill in "tx_powermail_pi1[field][inputpattern]" with "test"
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I should see 6 ".powermail_message_error > li" elements
    Then I fill in "tx_powermail_pi1[field][inputpattern]" with "http://www.test.de"
    Then I fill in "tx_powermail_pi1[field][firstname]" with "Sonntag"
    Then I select "gelb" from "tx_powermail_pi1[field][email]"
    Then I select "blau" from "tx_powermail_pi1[field][selectmulti][]"
    Then I additionally select "lila" from "tx_powermail_pi1[field][selectmulti][]"
    Then I check "tx_powermail_pi1[field][validation][]"
    Then I select "pink" from "tx_powermail_pi1[field][marker]"
    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Christian"
    Then I should see "Sonntag"
    Then I should see "test"
    Then I should see "http://www.test.de"
    Then I should see "gelb"
    Then I should see "blau"
    Then I should see "lila"
    Then I should see "pink"
    And I press "Weiter"

    Then I should see "Danke, Ihre Eingaben:"
    Then I should see "Christian"
    Then I should see "Sonntag"
    Then I should see "test"
    Then I should see "http://www.test.de"
    Then I should see "gelb"
    Then I should see "blau"
    Then I should see "lila"
    Then I should see "pink"