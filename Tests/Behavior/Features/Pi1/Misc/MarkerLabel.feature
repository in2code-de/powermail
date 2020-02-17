# Features/Pi1/Misc/MarkerLabel.feature
@Pi1 @Pi1Misc @Pi1MiscMarkerLabel
Feature: MarkerLabel

  Scenario: Check if labels are shown after submit
    Given I am on "/powermail/pi1/misc/label-marker/marker-label-in-rte-shortform"
    And I press "Jetzt Absenden"
    Then I should see "Vorname: Alex"
    Then I should see "Nachname: Kellner"
    Then I should see "E-Mail: alexander.kellner@einpraegsam.net"
