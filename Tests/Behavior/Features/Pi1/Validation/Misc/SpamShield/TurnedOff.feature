# Features/Pi1/Validation/SpamShieldValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationMisc @Pi1ValidationMiscSpamShield @Pi1ValidationMiscSpamShieldTurnedOff
Feature: TurnedOff

  # Test turned off Spamshield Form
  Scenario: Validator should NOT calculate 92% chance of spam and should NOt prevent submit
    Given I am on "/index.php?id=146"
    Then I should see "Name"
    Then I should see "E-Mail"
    Then I should see "Text"
    Then the "tx_powermail_pi1[field][name]" field should contain "Viagra"
    Then the "tx_powermail_pi1[field][e_mail]" field should contain "Viagra"

    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should not see "Spam-Wahrscheinlichkeit in dieser Nachricht!"
    Then I should not see "Spam in Nachricht vermutet: 92%"

    And I press "Weiter"

    Then I should see "Viagra"