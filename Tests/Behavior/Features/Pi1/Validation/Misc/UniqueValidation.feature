# Features/Pi1/Validation/UniqueValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationMisc @Pi1ValidationMiscUniqueValidation
Feature: UniqueValidation

  Scenario: Check if sending is disabled
    Given I am on "/index.php?id=118"
    Then I should see "Short Form Prefilled"
    And I press "Jetzt Absenden"

    Then I should see "Der Wert wurde bereits verwendet"
    When I fill in "tx_powermail_pi1[field][email]" with a random email
    And I press "Jetzt Absenden"

    Then I should see "Sind diese Eingaben korrekt?"
    And I press "Weiter"

    Then I should see "Thank you"