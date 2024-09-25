# Features/Pi1/Default/ShortFormAndRedirect.feature
@Pi1 @Pi1Default @Pi1DefaultShortFormAndRedirect
Feature: ShortFormAndRedirect

  # German
  Scenario: Searching for a DefaultForm that does exist in german
    Given I am on "/powermail/pi1/default/shortform-redirect"
    Then I should see "ShortForm (Redirect)"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"

  Scenario: Fill out DefaultForm and submit
    Given I am on "/powermail/pi1/default/shortform-redirect"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Tuana"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Koehler"
    When I fill in "tx_powermail_pi1[field][email]" with "Tuana.Koehler20@fake-yahoo.com"
    And I press "Jetzt Absenden"
    Then I should see "Sind diese Eingaben korrekt?"
    And I press "Weiter"
    Then I should see "Willkommen zum powermail Testparcour"

  # English
  Scenario: Searching for a DefaultForm that does exist in english
    Given I am on "/en/powermail/pi1/default/shortform-redirect"
    Then I should see "ShortForm (Redirect) EN"
    Then I should see "Firstname"
    Then I should see "Lastname"
    Then I should see "Email"

  Scenario: Fill out DefaultForm (english) and submit
    Given I am on "/en/powermail/pi1/default/shortform-redirect"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Tuana"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Köhler"
    When I fill in "tx_powermail_pi1[field][email]" with "Tuana.Koehler20@fake-yahoo.com"
    And I press "Submit"
    Then I should see "Are these values correct?"
    And I press "Next"
    Then I should see "Welcome to powermail Testparcour"
