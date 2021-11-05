# Features/Pi1/Misc/MarkerLabel.feature
@Pi1 @Pi1Misc @Pi1MiscMarkerLabel
Feature: MarkerLabel

  Scenario: Check if labels are shown after submit
    Given I am on "/powermail/pi1/misc/label-marker/marker-label-in-rte-shortform"
    And I press "Jetzt Absenden"
    Then I should see "Vorname: Tuana"
    Then I should see "Nachname: Köhler"
    Then I should see "E-Mail: Tuana.Koehler20@yahoo.com"
