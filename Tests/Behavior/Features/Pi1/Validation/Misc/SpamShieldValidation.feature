# Features/Pi1/Validation/SpamShieldValidation.feature
@Pi1ValidationSpamShieldValidation
Feature: SpamShieldValidation
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # Test Spamshield Form
  Scenario: Searching for a Form with a captcha
    Given I am on "/index.php?id=37"
    Then I should see "Name"
    Then I should see "E-Mail"
    Then I should see "Text"
    Then the "tx_powermail_pi1[field][name]" field should contain "Viagra"
    Then the "tx_powermail_pi1[field][e_mail]" field should contain "Viagra"

    And I press "Submit"

    Then I should see "Spam-Wahrscheinlichkeit in dieser Nachricht!"
    Then I should see "Spam in Nachricht vermutet: 92%"

    When I fill in "tx_powermail_pi1[field][e_mail]" with "test"
    And I press "Submit"

    Then I should see "Spam-Wahrscheinlichkeit in dieser Nachricht!"
    Then I should see "Spam in Nachricht vermutet: 90%"

    When I fill in "tx_powermail_pi1[field][text]" with "This is a text"
    When I fill in "tx_powermail_pi1[field][name]" with "Barbapappa"

    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Barbapappa"
    Then I should see "This is a text"
    Then I should see "test"

    And I press "Weiter"

    Then I should see "Barbapappa"
    Then I should see "This is a text"
    Then I should see "test"