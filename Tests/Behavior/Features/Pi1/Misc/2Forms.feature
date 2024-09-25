# Features/Pi1/Misc/2Forms.feature
@Pi1 @Pi1Misc @Pi1Misc2Forms
Feature: 2Forms

  Scenario: Check if 2 Powermail Forms work properly on one page
    Given I am on "/powermail/pi1/misc/2-forms-on-1-page/standard"
    Then I should see "String"
    Then I should see "Vorname"
    When I fill in "tx_powermail_pi1[field][string]" with "Tuana Koehler"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Tuana"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Koehler"
    When I fill in "tx_powermail_pi1[field][email]" with "Tuana.Koehler20@yahoo.com"
    And I press "Jetzt Absenden"

    Then I should not see "Tuana Koehler"
    Then I should see "Tuana"
    Then I should see "Koehler"
    Then I should see "Tuana.Koehler20@yahoo.com"
