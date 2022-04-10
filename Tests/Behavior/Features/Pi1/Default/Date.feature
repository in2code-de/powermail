# Features/Pi1/Default/Date.feature
@Pi1 @Pi1Default @Pi1DefaultDate
Feature: Date

  # Form is rendered?
  Scenario: Check if Date Form is rendered correctly
    Given I am on "/powermail/pi1/default/datedatetimetime"
    Then I should see "Date,Datetime,Time"
    Then I should see "String"
    Then I should see "Datum"
    Then I should see "Datum und Uhrzeit"
    Then I should see "Uhrzeit"
    Then I should see "Datum2"

  # Fill out form, see confirmation, go back and change something, see confirmation, see submit
  Scenario: Fill out DateForm and try to change dates
    Given I am on "/powermail/pi1/default/datedatetimetime"
    When I fill in "tx_powermail_pi1[field][string]" with "Datetest"
    When I fill in "tx_powermail_pi1[field][datum]" with "07/09/2014"
    When I assign the datetime "07.07.2014 18:00" in "tx_powermail_pi1[field][datumunduhrzeit]"
    When I fill in "tx_powermail_pi1[field][uhrzeit]" with "08:00pm"
    When I fill in "tx_powermail_pi1[field][datum2]" with "07/10/2014"
    And I press "Submit"
    Then I should see "Datetest"
    Then I should see "09.07.2014"
    Then I should see "07.07.2014 18:00"
    Then I should see "20:00"
    Then I should see "10.07.2014"
    And I press "Zurück"
    Then the "tx_powermail_pi1[field][string]" field should contain "Datetest"
    Then the "tx_powermail_pi1[field][datum]" field should contain "2014-07-09"
    Then the "tx_powermail_pi1[field][datumunduhrzeit]" field should contain "2014-07-07T18:00"
    Then the "tx_powermail_pi1[field][uhrzeit]" field should contain "20:00"
    Then the "tx_powermail_pi1[field][datum2]" field should contain "2014-07-10"
    Then I fill in "tx_powermail_pi1[field][string]" with "Date and Time Test"
    When I assign the datetime "09.07.2014 20:00" in "tx_powermail_pi1[field][datumunduhrzeit]"
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
