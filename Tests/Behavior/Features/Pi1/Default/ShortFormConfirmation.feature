# Features/Pi1/Default/ShortFormConfirmation.feature
@Pi1DefaultShortConfirmation
Feature: ShortFormConfirmation
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # German
  Scenario: Searching for a DefaultForm that does exist in german
    Given I am on "/index.php?id=72"
    Then I should see "ShortFormConfirmation"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"

  Scenario: Fill out DefaultForm and submit
    Given I am on "/index.php?id=72"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Martin"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Huber"
    When I fill in "tx_powermail_pi1[field][email]" with "mh@in2code.de"
    And I press "Jetzt Absenden"
    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Martin"
    Then I should see "Huber"
    Then I should see "mh@in2code.de"
    And I press "Weiter"
    Then I should see "Alle Werte:"
    Then I should see "Martin"
    Then I should see "Huber"
    Then I should see "mh@in2code.de"

  Scenario: Fill out DefaultForm, submit confirm and final submit
    Given I am on "/index.php?id=72"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Thomas"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Scheibitz"
    When I fill in "tx_powermail_pi1[field][email]" with "ts@in2code.de"
    And I press "Jetzt Absenden"
    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Thomas"
    Then I should see "Scheibitz"
    Then I should see "ts@in2code.de"
    And I press "Zurück"
    Then the "tx_powermail_pi1[field][firstname]" field should contain "Thomas"
    Then the "tx_powermail_pi1[field][lastname]" field should contain "Scheibitz"
    Then the "tx_powermail_pi1[field][email]" field should contain "ts@in2code.de"
    Then I fill in "tx_powermail_pi1[field][email]" with "scheibo@in2code.de"
    And I press "Jetzt Absenden"
    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Thomas"
    Then I should see "Scheibitz"
    Then I should see "scheibo@in2code.de"
    And I press "Weiter"
    Then I should see "Alle Werte:"
    Then I should see "Thomas"
    Then I should see "Scheibitz"
    Then I should see "scheibo@in2code.de"





  # English
  Scenario: Searching for a DefaultForm that does exist in english
    Given I am on "/index.php?id=72&L=1"
    Then I should see "ShortFormConfirmation EN"
    Then I should see "Firstname"
    Then I should see "Lastname"
    Then I should see "Email"

  Scenario: Fill out DefaultForm, submit confirm and final submit
    Given I am on "/index.php?id=72&L=1"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Martin"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Huber"
    When I fill in "tx_powermail_pi1[field][email]" with "mh@in2code.de"
    And I press "Submit"
    Then I should see "Are these values correct?"
    Then I should see "Martin"
    Then I should see "Huber"
    Then I should see "mh@in2code.de"
    And I press "Next"
    Then I should see "Thx for your email"
    Then I should see "Martin"
    Then I should see "Huber"
    Then I should see "mh@in2code.de"

  Scenario: Fill out DefaultForm, submit confirm go back, change a value, submit confirm and final submit
    Given I am on "/index.php?id=72&L=1"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Thomas"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Scheibitz"
    When I fill in "tx_powermail_pi1[field][email]" with "ts@in2code.de"
    And I press "Submit"
    Then I should see "Are these values correct?"
    Then I should see "Thomas"
    Then I should see "Scheibitz"
    Then I should see "ts@in2code.de"
    And I press "Previous"
    Then the "tx_powermail_pi1[field][firstname]" field should contain "Thomas"
    Then the "tx_powermail_pi1[field][lastname]" field should contain "Scheibitz"
    Then the "tx_powermail_pi1[field][email]" field should contain "ts@in2code.de"
    Then I fill in "tx_powermail_pi1[field][email]" with "scheibo@in2code.de"
    And I press "Submit"
    Then I should see "Are these values correct?"
    Then I should see "Thomas"
    Then I should see "Scheibitz"
    Then I should see "scheibo@in2code.de"
    And I press "Next"
    Then I should see "Thx for your email"
    Then I should see "Thomas"
    Then I should see "Scheibitz"
    Then I should see "scheibo@in2code.de"