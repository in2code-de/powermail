# Features/Pi1/Validation/Html5JsPhpValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationMandatory @Pi1ValidationMandatoryHtml5JsPhpValidation
Feature: Html5JsPhpValidation

  @javascript @Pi1ValidationMandatoryHtml5JsPhpValidation0
  Scenario: Check if mandatory Validation works (on PID8 with &L=0)
    Given I am on "/powermail/pi1/validation/mandatory/html5jsphp"
    Then I should see "Input"
    Then I should see "Input (Pattern http://ww)"
    Then I should see "Textarea"
    Then I should see "Select"
    Then I should see "Select Multi"
    Then I should see "Check"
    Then I should see "Radio"
    And I scroll "$[type='submit']" into view
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I should see "Eines dieser Felder muss ausgefüllt werden!"
    Then I fill in "tx_powermail_pi1[field][lastname]" with "Daniel"
    And I scroll "$[type='submit']" into view
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I should see "Eines dieser Felder muss ausgefüllt werden!"
    Then I fill in "tx_powermail_pi1[field][inputpattern]" with "test"
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I should see "Fehler in Validierung!"
    Then I should see "Eines dieser Felder muss ausgefüllt werden!"
    Then I fill in "tx_powermail_pi1[field][inputpattern]" with "http://www.test.de"
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I should not see "Fehler in Validierung!"
    Then I should see "Eines dieser Felder muss ausgefüllt werden!"
    Then I fill in "tx_powermail_pi1[field][date]" with "10/17/2014"
    Then I fill in "tx_powermail_pi1[field][firstname]" with "Boxhammer"
    Then I select "gelb" from "tx_powermail_pi1[field][email]"
    Then I select "blau" from "tx_powermail_pi1[field][selectmulti][]"
    Then I additionally select "lila" from "tx_powermail_pi1[field][selectmulti][]"
    Then I check "tx_powermail_pi1[field][validation][]"
    Then I select "rot" from "tx_powermail_pi1[field][marker]"
    Then I attach the file "test.txt" to "tx_powermail_pi1[field][file][]"
    Then I select "Deutschland" from "tx_powermail_pi1[field][marker02]"
    And I press "Submit"

    Then I should see "Sind diese Werte richtig?"
    Then I should see "Daniel"
    Then I should see "17.10.2014"
    Then I should see "Boxhammer"
    Then I should see "test"
    Then I should see "http://www.test.de"
    Then I should see "gelb"
    Then I should see "blau"
    Then I should see "lila"
    Then I should see "rot"
    Then I should see "DEU"
    And I press "Weiter"

    Then I should see "Danke, Ihre Eingaben:"
    Then I should see "Daniel"
    Then I should see "17.10.2014"
    Then I should see "Boxhammer"
    Then I should see "test"
    Then I should see "http://www.test.de"
    Then I should see "gelb"
    Then I should see "blau"
    Then I should see "lila"
    Then I should see "rot"
    Then I should see "DEU"

  @javascript @Pi1ValidationMandatoryHtml5JsPhpValidation1
  Scenario: Check if mandatory Validation works (on PID8 with &L=1)
    Given I am on "/en/powermail/pi1/validation/mandatory/html5jsphp"
    Then I should see "Input EN"
    Then I should see "Input (Pattern http://ww) EN"
    Then I should see "Textarea EN"
    Then I should see "Select EN"
    Then I should see "Select Multi EN"
    Then I should see "Check EN"
    Then I should see "Radio EN"
    And I scroll "$[type='submit']" into view
    And I press "Submit EN"

    Then I should see "This field must be filled!"
    Then I should see "One of these fields must be filled!"
    Then I fill in "tx_powermail_pi1[field][lastname]" with "Boxhammer"
    And I scroll "$[type='submit']" into view
    And I press "Submit EN"

    Then I should see "This field must be filled!"
    Then I should see "One of these fields must be filled!"
    Then I fill in "tx_powermail_pi1[field][inputpattern]" with "test"
    And I press "Submit EN"

    Then I should see "This field must be filled!"
    Then I should see "Error in validation!"
    Then I should see "One of these fields must be filled!"
    Then I fill in "tx_powermail_pi1[field][inputpattern]" with "http://www.test.de"
    And I press "Submit EN"

    Then I should see "This field must be filled!"
    Then I should not see "Error in validation!"
    Then I should see "One of these fields must be filled!"
    Then I fill in "tx_powermail_pi1[field][date]" with "10/17/2014"
    Then I fill in "tx_powermail_pi1[field][firstname]" with "Daniel"
    Then I select "yellow" from "tx_powermail_pi1[field][email]"
    Then I select "blue" from "tx_powermail_pi1[field][selectmulti][]"
    Then I additionally select "green" from "tx_powermail_pi1[field][selectmulti][]"
    Then I check "tx_powermail_pi1[field][validation][]"
    Then I select "red" from "tx_powermail_pi1[field][marker]"
    Then I attach the file "test.txt" to "tx_powermail_pi1[field][file][]"
    Then I select "Deutschland" from "tx_powermail_pi1[field][marker02]"
    And I press "Submit EN"

    Then I should see "Are these values correct?"
    Then I should see "Daniel"
    Then I should see "2014-10-17"
    Then I should see "Boxhammer"
    Then I should see "test"
    Then I should see "http://www.test.de"
    Then I should see "yellow"
    Then I should see "blue"
    Then I should see "green"
    Then I should see "red"
    Then I should see "DEU"
    And I press "Next"

    Then I should see "Thx, your values:"
    Then I should see "Daniel"
    Then I should see "2014-10-17"
    Then I should see "Boxhammer"
    Then I should see "test"
    Then I should see "http://www.test.de"
    Then I should see "yellow"
    Then I should see "blue"
    Then I should see "green"
    Then I should see "red"
    Then I should see "DEU"
