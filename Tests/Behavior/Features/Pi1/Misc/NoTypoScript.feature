# Features/Pi1/Misc/NoTypoScript.feature
@Pi1 @Pi1Misc @Pi1MiscNoTypoScript
Feature: NoTypoScript

  Scenario: Check if No-TypoScript-Message appears
    Given I am on "/powermail/pi1/misc/form-ohne-ts"
    Then I should see "Keine TypoScript-Konfiguration gefunden. Admin, haben Sie das statische Template \"Main\" eingebunden?"
