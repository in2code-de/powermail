# Features/Pi1/Misc/NoForm.feature
@Pi1 @Pi1Misc @Pi1MiscNoForm
Feature: NoForm

  Scenario: Check if No-Form Message appears
    Given I am on "/powermail/pi1/misc/no-form-chosen"
    Then I should see "No Form Chosen"
