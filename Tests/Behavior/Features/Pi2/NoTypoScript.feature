# Features/Pi2/NoTypoScript.feature
@Pi2 @Pi2NoTypoScript
Feature: NoTypoScript

  Scenario: Check if No-TypoScript-Message appears
    Given I am on "/powermail/pi2/list-ohne-ts"
    Then I should see "TypoScript benötigt"
