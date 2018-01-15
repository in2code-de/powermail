# Features/Pi1/Validation/DisabledByBreakerIp.feature
@Pi1 @Pi1Validation @Pi1ValidationMisc @Pi1ValidationMiscSpamShield @Pi1ValidationMiscSpamShieldDisabledByBreakerIp
Feature: DisabledByBreakerIp

  Scenario: Check if spamshield can be disabled by breaker IP
    Given I am on "/index.php?id=263"
    And I press "Submit"
    Then I should not see "Spam-Wahrscheinlichkeit in dieser Nachricht!"
