# Features/Pi1/Validation/UniqueValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationMisc @Pi1ValidationMiscUniqueValidation
Feature: UniqueValidation

  Scenario: Check if sending is disabled
    Given I am on "/powermail/pi1/validation/misc/uniquevalidation"
    Then I should see "Short Form Prefilled"
    And I press "Jetzt Absenden"

    Then I should see "Dieser Wert wird bereits verwendet"
    When I fill in "tx_powermail_pi1[field][email]" with a random email
    And I press "Jetzt Absenden"

    Then I should see "Sind diese Werte richtig?"
    And I press "Weiter"

    Then I should see "Thank you"
