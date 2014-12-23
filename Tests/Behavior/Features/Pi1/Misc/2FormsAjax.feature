# Features/Pi1/Misc/2FormsAjax.feature
@Pi1 @Pi1Misc @Pi1Misc2FormsAjax
Feature: 2Forms

  @javascript
  Scenario: Check if 2 Powermail Forms work properly on one page (with activated AJAX submit)
    Given I am on "/index.php?id=133"
    Then I should see "send to alex@in2code.de"
    Then I should see "send to alexander.kellner@einpraegsam.net"
    And I press "Jetzt Absenden"
    And I wait "a few" seconds

    Then I should not see "Form1 values"
    Then I should see "Form2 values"
    Then I should see "Alex"
    Then I should see "Kellner"