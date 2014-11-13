# Features/Pi1/Default/ShortFormDoubleOptinRedirect.feature
@Pi1 @Pi1Default @Pi1DefaultShortDoubleOptinRedirect
Feature: ShortFormDoubleOptinRedirect

  # German
  Scenario: Redirect should not work here
    Given I am on "/index.php?id=122"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"
    And I press "Jetzt Absenden"

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Alex"
    Then I should see "Kellner"
    Then I should see "alexander.kellner@einpraegsam.net"
    And I press "Weiter"
    Then I should see "Bitte überprüfen Sie Ihr E-Mail-Postfach und bestätigen Sie diese Aktion."