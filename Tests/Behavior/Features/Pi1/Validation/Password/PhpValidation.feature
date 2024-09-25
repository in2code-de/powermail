# Features/Pi1/Validation/PasswordPhpValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationPassword @Pi1ValidationPasswordPhpValidation
Feature: PasswordJsValidation

  Scenario: Check if mandatory Validation works (on &L=0)
    Given I am on "/powermail/pi1/validation/password/password-php"
    Then I should see "Name"
    Then I should see "Password"
    Then I should see "Bitte erneut eintragen"
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then I fill in "tx_powermail_pi1[field][name]" with "Daniel Boxhammer"
    Then I fill in "tx_powermail_pi1[field][password]" with "abc"
    And I press "Submit"

    Then I should see "Die beiden Passwort-Felder enthalten nicht den gleichen Wert!"
    Then I fill in "tx_powermail_pi1[field][password]" with "abc"
    Then I fill in "tx_powermail_pi1[field][password_mirror]" with "ab"
    And I press "Submit"

    Then I should see "Die beiden Passwort-Felder enthalten nicht den gleichen Wert!"
    Then I fill in "tx_powermail_pi1[field][password]" with "abc"
    Then I fill in "tx_powermail_pi1[field][password_mirror]" with "abc"
    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt"
    Then I should not see "abc"
    Then I should see "********"
    Then I should see "Daniel Boxhammer"
    And I press "Weiter"

    Then I should not see "abc"
    Then I should see "********"
    Then I should see "Daniel Boxhammer"
