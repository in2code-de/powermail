# Features/Pi1/Validation/ForeignValidator.feature
@Pi1 @Pi1Validation @Pi1ValidationMisc @Pi1ValidationMiscForeignValidator
Feature: ForeignValidator

  # Check if Validator class will work
  Scenario: Searching for a Form with extended Validators
    Given I am on "/index.php?id=195"
    Then I should see "Vorname"
    Then I fill in "tx_powermail_pi1[field][firstname]" with "Alexa"
    And I press "Jetzt Absenden"

    Then I should see 1 ".powermail_message_error > li" elements
    Then I should see "Firstname must be"
    Then I fill in "tx_powermail_pi1[field][firstname]" with "Alex"
    And I press "Jetzt Absenden"
    Then I should see "Validierung korrekt"
