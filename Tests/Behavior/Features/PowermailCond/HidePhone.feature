# Features/PowermailCond/HidePhone.feature
@PowermailCondHidePhone
Feature: HidePhone

  @javascript
  Scenario: Phone field should be hidden if email is filled
    Given I am on "/index.php?id=52"
    And I wait "2" seconds
    Then I should see "E-Mail"

    And I press "Submit"
    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I should see "E-Mail"
    Then I should see "Telefon"

    When I fill in "tx_powermail_pi1[field][name]" with "Roland Waldner"
    And I wait "2" seconds
    When I fill in "tx_powermail_pi1[field][e_mail]" with "rw@in2code.de"
    And I wait "2" seconds
    When I fill in "tx_powermail_pi1[field][text]" with "This is my short test."
    And I wait "2" seconds
    When I select "IT" from "tx_powermail_pi1[field][marker]"
    And I wait "2" seconds
    Then I should not see "Telefon"
    And I press "Submit"

    Then I should see "Submit successful"
    Then I should see "Roland Waldner"
    Then I should see "rw@in2code.de"
    Then I should see "This is my short test."
    Then I should see "IT"