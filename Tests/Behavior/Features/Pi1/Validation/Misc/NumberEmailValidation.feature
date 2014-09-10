# Features/Pi1/Validation/NumberEmailValidation.feature
@Pi1ValidationNumberEmailValidation
Feature: NumberEmailValidation
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # Test NumberEmailValidation Form with different values
  Scenario: Show NumberEmailValidation Form
    Given I am on "/index.php?id=69"
    Then I should see "Number"
    Then I should see "E-Mail"
    When I fill in "tx_powermail_pi1[field][number]" with "abc"
    When I fill in "tx_powermail_pi1[field][email]" with "abc"
    And I press "Einfach Leer Absenden"

    Then I should see "Bitte nur Nummern eintragen!"
    Then I should see "Keine gültige E-Mail-Adresse!"
    When I fill in "tx_powermail_pi1[field][number]" with "123"
    When I fill in "tx_powermail_pi1[field][email]" with "test@test.de"
    And I press "Einfach Leer Absenden"

    Then I should not see "Bitte nur Nummern eintragen!"
    Then I should not see "Keine gültige E-Mail-Adresse!"
    Then I should see "123"
    Then I should see "test@test.de"
    Then I should see "Submit hat funktioniert"

  # Test NumberEmailValidation Form with empty values
  Scenario: Show NumberEmailValidation Form
    Given I am on "/index.php?id=69"
    Then I should see "Number"
    Then I should see "E-Mail"
    And I press "Einfach Leer Absenden"
    Then I should see "Submit hat funktioniert"
