# Features/Pi1/Default/ShortForm.feature
@Pi1DefaultShortFormAjax
Feature: ShortFormAjax
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # L=0
  Scenario: Searching for a DefaultForm that does exist in german (&L=0)
    Given I am on "/index.php?id=9"
    Then I should see "ShortForm (AJAX)"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"

  @javascript @Pi1DefaultShortFormAjax0
  Scenario: Test AJAX submit in german (&L=0)
    Given I am on "/index.php?id=9"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Marcus"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Schwemer"
    When I fill in "tx_powermail_pi1[field][email]" with "ms@in2code.de"
    And I press "Jetzt Absenden"
    And I wait "5" seconds

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Marcus"
    Then I should see "ms@in2code.de"
    Then I should not see "Error, this text is only viewable if AJAX"

    And I press "Zurück"
    And I wait "5" seconds
    Then the "tx_powermail_pi1[field][firstname]" field should contain "Marcus"
    Then the "tx_powermail_pi1[field][email]" field should contain "ms@in2code.de"

    When I fill in "tx_powermail_pi1[field][email]" with "marcus@in2code.de"
    And I press "Jetzt Absenden"
    And I wait "5" seconds

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Marcus"
    Then I should see "marcus@in2code.de"
    Then I should not see "Error, this text is only viewable if AJAX"

    And I press "Weiter"
    And I wait "5" seconds

    Then I should see "Danke, das sind Ihre Werte:"
    Then I should see "Marcus"
    Then I should see "marcus@in2code.de"
    Then I should not see "Error, this text is only viewable if AJAX"

  @javascript @Pi1DefaultShortFormAjax1
  Scenario: Searching for a DefaultForm that does exist in english (&L=1)
    Given I am on "/index.php?id=9&L=1"
    Then I should see "ShortForm (AJAX) EN"
    Then I should see "Firstname"
    Then I should see "Lastname"
    Then I should see "Email"

    When I fill in "tx_powermail_pi1[field][firstname]" with "David"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Richter"
    When I fill in "tx_powermail_pi1[field][email]" with "dr@in2code.de"
    And I press "Submit"
    And I wait "7" seconds

    Then I should see "Are these values correct?"
    Then I should see "David"
    Then I should see "dr@in2code.de"
    Then I should not see "Error, this text is only viewable if AJAX"

    And I press "Previous"
    And I wait "7" seconds
    Then the "tx_powermail_pi1[field][firstname]" field should contain "David"
    Then the "tx_powermail_pi1[field][email]" field should contain "dr@in2code.de"

    When I fill in "tx_powermail_pi1[field][email]" with "dave@in2code.de"
    And I press "Submit"
    And I wait "7" seconds

    Then I should see "Are these values correct?"
    Then I should see "David"
    Then I should see "dave@in2code.de"
    Then I should not see "Error, this text is only viewable if AJAX"

    And I press "Next"
    And I wait "7" seconds

    Then I should see "Thx, your values:"
    Then I should see "David"
    Then I should see "dave@in2code.de"
    Then I should not see "Error, this text is only viewable if AJAX"