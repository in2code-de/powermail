# Features/TYPO3/Start.feature
@TYPO3 @TYPO3Start
Feature: Search
  Basic test to see if TYPO3 instance is working

  Scenario: Searching for the homepage that does exist
    Given I am on "/"
    Then I should see "Willkommen zum powermail Testparcour"
    Given I am on "/en/"
    Then I should see "Welcome to powermail Testparcour"
