# Features/Pi1/Validation/ExtendedPhpValidation.feature
@Pi1ValidationExtendedPhpValidation
Feature: ExtendedPhpValidation
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # Check if extended Validators will work (PHP only)
  Scenario: Searching for a Form with extended Validators
    Given I am on "/index.php?id=60"
    Then I should see "Name"
    Then I should see "Email"
    Then I should see "ZIP (80000 or higher)"
    Then I should see "This is a complete new Field"
    Then I should see "Your Text"
    Then I fill in "tx_powermail_pi1[field][yourtext]" with "Andy Kräuter"
    And I press "Submit"

    Then I should see 2 ".powermail_message_error > li" elements
    Then I should see "Keine gültige E-Mail-Adresse!"
    Then I should see "Bitte eine bayerische PLZ eintragen"
    Then I should see "ZIP (80000 or higher)"
    Then I fill in "tx_powermail_pi1[field][email]" with "test@test.de"
    Then I fill in "tx_powermail_pi1[field][zip]" with "80001"

    And I press "Submit"
    Then I should see "Validierung korrekt"
    Then I should not see "Keine gültige E-Mail-Adresse!"
    Then I should not see "Bitte eine bayerische PLZ eintragen"
    Then I should see "80001"
    Then I should see "test@test.de"
    Then I should see "Andy Kräuter"
    Then I should see "Alex Kellner"