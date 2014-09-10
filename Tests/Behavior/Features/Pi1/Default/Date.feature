# Features/Pi1/Default/Date.feature
@Pi1DefaultDate
Feature: Date
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # Form is rendered?
  Scenario: Check if Date Form is rendered correctly
    Given I am on "/index.php?id=28"
    Then I should see "Date,Datetime,Time"
    Then I should see "String"
    Then I should see "Datum"
    Then I should see "Datum und Uhrzeit"
    Then I should see "Uhrzeit"
    Then I should see "Datum2"

  # Fill out form, see confirmation, go back and change something, see confirmation, see submit
  Scenario: Fill out DateForm and try to change dates
    Given I am on "/index.php?id=28"
    When I fill in "tx_powermail_pi1[field][string]" with "Datetest"
    When I fill in "tx_powermail_pi1[field][datum]" with "09.07.2014"
    When I fill in "tx_powermail_pi1[field][datumunduhrzeit]" with "07.07.2014 18:00"
    When I fill in "tx_powermail_pi1[field][uhrzeit]" with "20:00"
    When I fill in "tx_powermail_pi1[field][datum2]" with "10.07.2014"
    And I press "Submit"
    Then I should see "Datetest"
    Then I should see "09.07.2014"
    Then I should see "07.07.2014 18:00"
    Then I should see "20:00"
    Then I should see "10.07.2014"
    And I press "Zurück"
    Then the "tx_powermail_pi1[field][string]" field should contain "Datetest"
    Then the "tx_powermail_pi1[field][datum]" field should contain "09.07.2014"
    Then the "tx_powermail_pi1[field][datumunduhrzeit]" field should contain "07.07.2014 18:00"
    Then the "tx_powermail_pi1[field][uhrzeit]" field should contain "20:00"
    Then the "tx_powermail_pi1[field][datum2]" field should contain "10.07.2014"
    Then I fill in "tx_powermail_pi1[field][string]" with "Date and Time Test"
    Then I fill in "tx_powermail_pi1[field][datumunduhrzeit]" with "09.07.2014 20:00"
    And I press "Submit"
    Then I should see "Date and Time Test"
    Then I should see "09.07.2014"
    Then I should see "09.07.2014 20:00"
    Then I should see "20:00"
    Then I should see "10.07.2014"
    And I press "Weiter"
    Then I should see "Date and Time Test"
    Then I should see "09.07.2014"
    Then I should see "09.07.2014 20:00"
    Then I should see "20:00"
    Then I should see "10.07.2014"