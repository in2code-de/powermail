# Features/Pi2/Detail.feature
@Pi2 @Pi2Detail
Feature: Detail

  @Pi2DetailEntry
  Scenario: Add a new dummy Entry
    Given I am on "/powermail/pi2/standard/form"
    When I fill in "tx_powermail_pi1[field][string]" with "Daniel Boxhammer"
    When I fill in "tx_powermail_pi1[field][textarea]" with "Das ist ein Test"
    When I select "Sandra" from "tx_powermail_pi1[field][marker]"
    When I select "Alex" from "tx_powermail_pi1[field][selectmulti][]"
    When I additionally select "Olli" from "tx_powermail_pi1[field][selectmulti][]"
    When I check "tx_powermail_pi1[field][check][]"
    When I select "Olli" from "tx_powermail_pi1[field][radio]"
    And I press "Submit"
    And I press "Weiter"
    Then I should see "Daniel Boxhammer"

  @Pi2DetailCheckEntry
  Scenario: Follow detaillink in listview
    Given I am on "/powermail/pi2/standard/list"
    Then I follow "Details"
    Then I wait "a few" seconds
    Then I should see "String"
    Then I should see "Daniel Boxhammer"
    Then I should see "Das ist ein Test"
    Then I should see "Sandra"
    Then I should see "Alex"
    Then I should see "Olli"
