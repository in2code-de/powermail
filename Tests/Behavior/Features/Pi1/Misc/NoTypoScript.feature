# Features/Pi1/Misc/NoTypoScript.feature
@Pi1MiscNoTypoScript
Feature: NoTypoScript

  Scenario: Check if No-TypoScript-Message appears
    Given I am on "/index.php?id=13"
    Then I should see "Kein TypoScript gefunden"