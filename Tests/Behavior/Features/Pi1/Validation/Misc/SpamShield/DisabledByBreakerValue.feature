# Features/Pi1/Validation/DisabledByBreakerValue.feature
@Pi1 @Pi1Validation @Pi1ValidationMisc @Pi1ValidationMiscSpamShield @Pi1ValidationMiscSpamShieldDisabledByBreakerValue
Feature: DisabledByBreakerValue

  Scenario: Check if spamshield can be disabled by breaker Value
    Given I am on "/index.php?id=264"
    And I press "Submit"
    Then I should not see "Spam-Wahrscheinlichkeit in dieser Nachricht!"
