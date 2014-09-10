# Features/Pi1/Default/ShortFormDoubleOptin.feature
@Pi1DefaultShortDoubleOptin
Feature: ShortFormDoubleOptin
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # German
  Scenario: Searching for a DefaultForm that does exist in german
    Given I am on "/index.php?id=65"
    Then I should see "ShortFormAndRedirect"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"

  Scenario: Fill out DefaultForm and submit
    Given I am on "/index.php?id=65"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Alex"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Kellner"
    When I fill in "tx_powermail_pi1[field][email]" with "alex@in2code.de"
    And I press "Jetzt Absenden"
    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Alex"
    Then I should see "Kellner"
    Then I should see "alex@in2code.de"
    And I press "Weiter"
    Then I should see "Bitte überprüfen Sie Ihr E-Mail-Postfach und bestätigen Sie diese Aktion."