# Features/Pi1/Default/Default.feature
@Pi1 @Pi1Default @Pi1DefaultDefault
Feature: AllFields
  Check all default fields

  Scenario: Check if AllFields Form is rendered correctly
    Given I am on "/index.php?id=140"
    Then I should see "Input"
    Then I should see "Textarea"
    Then I should see "Select"
    Then I should see "Check"
    Then I should see "Radio"

    When I fill in "tx_powermail_pi1[field][input]" with "This is an input"
    When I fill in "tx_powermail_pi1[field][marker]" with "This is a textarea"
    When I select "Olli" from "tx_powermail_pi1[field][marker_01]"
    And I press "Submit"

    Then I should see "This is an input"
    Then I should see "This is a textarea"
    Then I should see "Olli"