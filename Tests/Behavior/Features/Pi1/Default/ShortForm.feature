# Features/Pi1/Default/ShortForm.feature
@Pi1 @Pi1Default @Pi1DefaultShortForm
Feature: ShortForm

  # L=0
  Scenario: Searching for a DefaultForm that does exist in german
    Given I am on "/powermail/pi1/default/shortform"
    Then I should see "ShortForm"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"

  Scenario: Fill out DefaultForm and submit
    Given I am on "/powermail/pi1/default/shortform"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Kim"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Salow"
    When I fill in "tx_powermail_pi1[field][email]" with "Kim_Salow@fake-gmail-128.com"
    And I press "Jetzt Absenden"
    Then I should see "Kim"
    Then I should see "Salow"
    Then I should see "Kim_Salow@fake-gmail-128.com"

# L=1
  Scenario: Searching for a DefaultForm that does exist in english
    Given I am on "/en/powermail/pi1/default/shortform"
    Then I should see "ShortForm EN"
    Then I should see "Firstname"
    Then I should see "Lastname"
    Then I should see "Email"

  Scenario: Fill out DefaultForm (english) and submit
    Given I am on "/en/powermail/pi1/default/shortform"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Kim"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Salow"
    When I fill in "tx_powermail_pi1[field][email]" with "Kim_Salow@fake-gmail-128.com"
    And I press "Submit"
    Then I should see "Kim"
    Then I should see "Salow"
    Then I should see "Kim_Salow@fake-gmail-128.com"
