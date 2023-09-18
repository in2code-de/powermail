# Features/Pi1/Default/ShortFormConfirmation.feature
@Pi1 @Pi1Default @Pi1DefaultShortConfirmation
Feature: ShortFormConfirmation

  # German
  Scenario: Searching for a DefaultForm that does exist in german
    Given I am on "/powermail/pi1/default/shortform-confirmation"
    Then I should see "ShortForm (Confirmation)"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"

  Scenario: Fill out DefaultForm and submit
    Given I am on "/powermail/pi1/default/shortform-confirmation"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Daniel"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Boxhammer"
    When I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Jetzt Absenden"
    Then I should see "Sind diese Werte richtig?"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Weiter"
    Then I should see "Alle Werte:"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"

  Scenario: Fill out DefaultForm, submit confirm and final submit
    Given I am on "/powermail/pi1/default/shortform-confirmation"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Daniel"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Boxhammer"
    When I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Jetzt Absenden"
    Then I should see "Sind diese Werte richtig?"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Zurück"
    Then the "tx_powermail_pi1[field][firstname]" field should contain "Daniel"
    Then the "tx_powermail_pi1[field][lastname]" field should contain "Boxhammer"
    Then the "tx_powermail_pi1[field][email]" field should contain "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer@fake-yahoo-10000.com"
    And I press "Jetzt Absenden"
    Then I should see "Sind diese Werte richtig?"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer@fake-yahoo-10000.com"
    And I press "Weiter"
    Then I should see "Alle Werte:"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer@fake-yahoo-10000.com"





  # English
  Scenario: Searching for a DefaultForm that does exist in english
    Given I am on "/en/powermail/pi1/default/shortform-confirmation"
    Then I should see "ShortForm (Confirmation) EN"
    Then I should see "Firstname"
    Then I should see "Lastname"
    Then I should see "Email"

  Scenario: Fill out DefaultForm, submit confirm and final submit
    Given I am on "/en/powermail/pi1/default/shortform-confirmation"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Daniel"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Boxhammer"
    When I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Submit"
    Then I should see "Are these values correct?"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Next"
    Then I should see "Thx for your email"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"

  Scenario: Fill out DefaultForm, submit confirm go back, change a value, submit confirm and final submit
    Given I am on "/en/powermail/pi1/default/shortform-confirmation"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Daniel"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Boxhammer"
    When I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Submit"
    Then I should see "Are these values correct?"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Previous"
    Then the "tx_powermail_pi1[field][firstname]" field should contain "Daniel"
    Then the "tx_powermail_pi1[field][lastname]" field should contain "Boxhammer"
    Then the "tx_powermail_pi1[field][email]" field should contain "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer@fake-yahoo-10000.com"
    And I press "Submit"
    Then I should see "Are these values correct?"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer@fake-yahoo-10000.com"
    And I press "Next"
    Then I should see "Thx for your email"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer@fake-yahoo-10000.com"
