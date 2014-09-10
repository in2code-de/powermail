# Features/Pi1/Misc/2Forms.feature
@Pi1Misc2Forms
Feature: 2Forms

  Scenario: Check if 2 Powermail Forms work properly on one page
    Given I am on "/index.php?id=54"
    Then I should see "String"
    Then I should see "Vorname"
    When I fill in "tx_powermail_pi1[field][string]" with "Sandra Pohl"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Sandra"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Pohl"
    When I fill in "tx_powermail_pi1[field][email]" with "sp@in2code.de"
    And I press "Jetzt Absenden"

    Then I should not see "Sandra Pohl"
    Then I should see "Sandra"
    Then I should see "Pohl"
    Then I should see "sp@in2code.de"