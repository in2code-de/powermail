# Features/Pi1/Misc/NoForm.feature
@Pi1 @Pi1Misc @Pi1MiscExcludeFromPowermailAll
Feature: ExcludeFromPowermailAll

  Scenario: Check if values can be removed from {powermail_all} via configuration
    Given I am on "/index.php?id=109"
    Then I should see "Admin Only"

    When I fill in "tx_powermail_pi1[field][captcha]" with "123"
    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Sendername"
    Then I should see "alexander.kellner@einpraegsam.net"
    Then I should not see "Admin Only Value"
    Then I should not see "This is the hidden value"
    Then I should not see "123"
    And I press "Weiter"

    Then I should see "Danke, Ihre Eingaben:"
    Then I should see "Sendername"
    Then I should see "alexander.kellner@einpraegsam.net"
    Then I should see "This is the hidden value"
    Then I should not see "Admin Only Value"
    Then I should not see "123"