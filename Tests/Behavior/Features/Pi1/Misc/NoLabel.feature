# Features/Pi1/Misc/NoLabel.feature
@Pi1 @Pi1Misc @Pi1MiscNoLabel
Feature: NoLabel

  @javascript
  Scenario: Check if labels for form, fieldset and fields are not viewable
    Given I am on "/index.php?id=191"
    Then I should not see "Label Form"
    Then I should not see "Label Page"
    Then I should not see "Label Input"
    Then I should not see "Label Check"
    Then I should not see "Label Radio"
