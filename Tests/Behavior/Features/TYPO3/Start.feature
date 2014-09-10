# Features/TYPO3/Start.feature
@TYPO3Start
Feature: Search
  In order to see a word definition
  As a website user
  I need to be able to search for a word

  Scenario: Searching for the homepage that does exist
    Given I am on "/index.php?id=2"
    Then I should see "Willkommen zum powermail Testparcour"
    Given I am on "/index.php?id=2&L=1"
    Then I should see "Welcome to powermail Testparcour"