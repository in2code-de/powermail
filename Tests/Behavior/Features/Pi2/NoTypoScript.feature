# Features/Pi2/NoTypoScript.feature
@Pi2 @Pi2NoTypoScript
Feature: NoTypoScript

  Scenario: Check if No-TypoScript-Message appears
    Given I am on "/index.php?id=12"
    Then I should see "Kein TypoScript gefunden"