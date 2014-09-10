# Features/Pi1/Default/ShortFormAndRedirect.feature
@Pi1DefaultShortFormAndRedirect
Feature: ShortFormAndRedirect
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # German
  Scenario: Searching for a DefaultForm that does exist in german
    Given I am on "/index.php?id=51"
    Then I should see "ShortFormAndRedirect"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"

  Scenario: Fill out DefaultForm and submit
    Given I am on "/index.php?id=51"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Alex"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Kellner"
    When I fill in "tx_powermail_pi1[field][email]" with "alex@in2code.de"
    And I press "Jetzt Absenden"
    Then I should see "Sind diese Eingaben korrekt?"
    And I press "Weiter"
    Then I should see "Willkommen zum powermail Testparcour"

  # English
  Scenario: Searching for a DefaultForm that does exist in english
    Given I am on "/index.php?id=51&L=1"
    Then I should see "ShortFormAndRedirect EN"
    Then I should see "Firstname"
    Then I should see "Lastname"
    Then I should see "Email"

  Scenario: Fill out DefaultForm (english) and submit
    Given I am on "/index.php?id=51&L=1"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Silke"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Kellner"
    When I fill in "tx_powermail_pi1[field][email]" with "silke@in2code.de"
    And I press "Submit"
    Then I should see "Are these values correct?"
    And I press "Next"
    Then I should see "Welcome to powermail Testparcour"