# Features/Pi1/Validation/PasswordJsValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationPassword @Pi1ValidationPasswordJsValidation
Feature: PasswordJsValidation

  @javascript
  Scenario: Check if mandatory Validation works (on &L=0)
    Given I am on "/powermail/pi1/validation/password/password-all"
    Then I should see "Name"
    Then I should see "Password"
    Then I should see "Bitte wiederholen"
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I fill in "tx_powermail_pi1[field][name]" with "Daniel Boxhammer"
    Then I fill in "tx_powermail_pi1[field][password]" with "abc"
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I fill in "tx_powermail_pi1[field][password_mirror]" with "ab"
    And I press "Submit"

    Then I should see "Die Passwort-Felder stimmen nicht überein!"
    Then I fill in "tx_powermail_pi1[field][password_mirror]" with "abc"
    And I press "Submit"

    Then I should see "Sind diese Werte richtig?"
    Then I should not see "abc"
    Then I should see "********"
    Then I should see "Daniel Boxhammer"
    And I press "Weiter"

    Then I should not see "abc"
    Then I should see "********"
    Then I should see "Daniel Boxhammer"
