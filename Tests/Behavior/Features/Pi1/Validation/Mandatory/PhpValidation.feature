# Features/Pi1/Validation/PhpValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationMandatory @Pi1ValidationMandatoryPhpValidation
Feature: PhpValidation

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
    Then I should see 10 ".powermail_message_error > li" elements
    Then I fill in "tx_powermail_pi1[field][lastname]" with "Christian"
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I should see 9 ".powermail_message_error > li" elements
    Then I fill in "tx_powermail_pi1[field][inputpattern]" with "test"
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I should see 9 ".powermail_message_error > li" elements
    Then I fill in "tx_powermail_pi1[field][inputpattern]" with "http://www.test.de"
    Then I fill in "tx_powermail_pi1[field][firstname]" with "Sonntag"
    Then I fill in "tx_powermail_pi1[field][date]" with "17.10.2014"
    Then I select "gelb" from "tx_powermail_pi1[field][email]"
    Then I select "blau" from "tx_powermail_pi1[field][selectmulti][]"
    Then I additionally select "lila" from "tx_powermail_pi1[field][selectmulti][]"
    Then I check "tx_powermail_pi1[field][validation][]"
    Then I select "pink" from "tx_powermail_pi1[field][marker]"
    When I attach the file "test.txt" to "tx_powermail_pi1[field][file][]"
    Then I select "Deutschland" from "tx_powermail_pi1[field][marker_02]"
    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "17.10.2014"
    Then I should see "Christian"
    Then I should see "Sonntag"
    Then I should see "test"
    Then I should see "http://www.test.de"
    Then I should see "gelb"
    Then I should see "blau"
    Then I should see "lila"
    Then I should see "pink"
    Then I should see "DEU"
    And I press "Weiter"

    Then I should see "Danke, Ihre Eingaben:"
    Then I should see "17.10.2014"
    Then I should see "Christian"
    Then I should see "Sonntag"
    Then I should see "test"
    Then I should see "http://www.test.de"
    Then I should see "gelb"
    Then I should see "blau"
    Then I should see "lila"
    Then I should see "pink"
    Then I should see "DEU"