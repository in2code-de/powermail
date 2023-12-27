# Features/Pi1/Default/ShortForm.feature
@Pi1 @Pi1Default @Pi1DefaultShortFormAjax
Feature: ShortFormAjax

  # L=0
  Scenario: Searching for a DefaultForm that does exist in german (&L=0)
    Given I am on "/powermail/pi1/default/shortform-ajax"
    Then I should see "ShortForm (AJAX)"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"

  @javascript @Pi1DefaultShortFormAjax0
  Scenario: Test AJAX submit in german (&L=0)
    Given I am on "/powermail/pi1/default/shortform-ajax"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Daniel"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Boxhammer"
    When I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer@fake-yahoo-10000.com"
    And I press "Jetzt Absenden"
    And I wait "a few" seconds

    Then I should see "Sind diese Werte richtig?"
    Then I should see "Daniel"
    Then I should see "Daniel_Boxhammer@fake-yahoo-10000.com"
    Then I should not see "Error, this text is only viewable if AJAX"

    And I press "Zurück"
    And I wait "a few" seconds
    Then the "tx_powermail_pi1[field][firstname]" field should contain "Daniel"
    Then the "tx_powermail_pi1[field][email]" field should contain "Daniel_Boxhammer@fake-yahoo-10000.com"

    When I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Jetzt Absenden"
    And I wait "a few" seconds

    Then I should see "Sind diese Werte richtig?"
    Then I should see "Daniel"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I should not see "Error, this text is only viewable if AJAX"

    And I press "Weiter"
    And I wait "a few" seconds

    Then I should see "Danke, das sind Ihre Werte:"
    Then I should see "Daniel"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I should not see "Error, this text is only viewable if AJAX"

  @javascript @Pi1DefaultShortFormAjax1
  Scenario: Searching for a DefaultForm that does exist in english (&L=1)
    Given I am on "/en/powermail/pi1/default/shortform-ajax"
    Then I should see "ShortForm (AJAX) EN"
    Then I should see "Firstname"
    Then I should see "Lastname"
    Then I should see "Email"

    When I fill in "tx_powermail_pi1[field][firstname]" with "Daniel"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Boxhammer"
    When I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer@fake-yahoo-10000.com"
    And I press "Submit"
    And I wait "a few" seconds

    Then I should see "Are these values correct?"
    Then I should see "Daniel"
    Then I should see "Daniel_Boxhammer@fake-yahoo-10000.com"
    Then I should not see "Error, this text is only viewable if AJAX"

    And I press "Previous"
    And I wait "a few" seconds
    Then the "tx_powermail_pi1[field][firstname]" field should contain "Daniel"
    Then the "tx_powermail_pi1[field][email]" field should contain "Daniel_Boxhammer@fake-yahoo-10000.com"

    When I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Submit"
    And I wait "a few" seconds

    Then I should see "Are these values correct?"
    Then I should see "Daniel"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I should not see "Error, this text is only viewable if AJAX"

    And I press "Next"
    And I wait "a few" seconds

    Then I should see "Thx, your values:"
    Then I should see "Daniel"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I should not see "Error, this text is only viewable if AJAX"
