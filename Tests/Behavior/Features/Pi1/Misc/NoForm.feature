# Features/Pi1/Misc/NoForm.feature
@Pi1 @Pi1Misc @Pi1MiscNoForm
Feature: NoForm

  Scenario: Check if No-Form Message appears
    Given I am on "/index.php?id=47"
    Then I should see "No Form Chosen"