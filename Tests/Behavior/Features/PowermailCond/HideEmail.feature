# Features/PowermailCond/HideEmail.feature
@PowermailCondHideEmail
Feature: HideEmail

  @javascript
  Scenario: Email field should be hidden if phone is filled
    Given I am on "/index.php?id=52"
    And I wait "2" seconds
    Then I should see "E-Mail"

    And I press "Submit"
    Then I should see "Dieses Feld muss ausgefüllt werden!"

    When I fill in "tx_powermail_pi1[field][name]" with "Irene Höppner"
    And I wait "2" seconds
    When I fill in "tx_powermail_pi1[field][telefon]" with "12345"
    And I wait "2" seconds
    When I fill in "tx_powermail_pi1[field][text]" with "Das ist mein Test"
    And I wait "2" seconds
    When I select "IT" from "tx_powermail_pi1[field][marker]"
    And I wait "2" seconds
    Then I should not see "E-Mail"
    And I press "Submit"

    Then I should see "Submit successful"
    Then I should see "Irene Höppner"
    Then I should see "12345"
    Then I should see "Das ist mein Test"
    Then I should see "IT"