# Features/Pi1/Default/ShortFormDoubleOptinRedirect.feature
@Pi1 @Pi1Default @Pi1DefaultShortDoubleOptinRedirect
Feature: ShortFormDoubleOptinRedirect

  # German
  Scenario: Redirect should not work here
    Given I am on "/powermail/pi1/default/shortform-doubleoptin-redirect"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"
    And I press "Jetzt Absenden"

    Then I should see "Sind diese Werte richtig?"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Weiter"
    Then I should see "Bitte schauen Sie in Ihrem Posteingang nach und bestätigen Sie diese Aktion."
