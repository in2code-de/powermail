# Features/Pi1/Default/ShortForm.feature
@Pi1DefaultShortForm
Feature: ShortForm
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # L=0
  Scenario: Searching for a DefaultForm that does exist in german
    Given I am on "/index.php?id=15"
    Then I should see "ShortForm"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"

  Scenario: Fill out DefaultForm and submit
    Given I am on "/index.php?id=15"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Alex"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Kellner"
    When I fill in "tx_powermail_pi1[field][email]" with "alex@in2code.de"
    And I press "Jetzt Absenden"
    Then I should see "Alex"
    Then I should see "Kellner"
    Then I should see "alex@in2code.de"

# L=1
  Scenario: Searching for a DefaultForm that does exist in english
    Given I am on "/index.php?id=15&L=1"
    Then I should see "ShortForm EN"
    Then I should see "Firstname"
    Then I should see "Lastname"
    Then I should see "Email"

  Scenario: Fill out DefaultForm (english) and submit
    Given I am on "/index.php?id=15&L=1"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Silke"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Kellner"
    When I fill in "tx_powermail_pi1[field][email]" with "silke@in2code.de"
    And I press "Submit"
    Then I should see "Silke"
    Then I should see "Kellner"
    Then I should see "silke@in2code.de"