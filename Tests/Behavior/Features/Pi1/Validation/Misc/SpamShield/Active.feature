# Features/Pi1/Validation/SpamShieldValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationMisc @Pi1ValidationMiscSpamShield @Pi1ValidationMiscSpamShieldActive
Feature: Active

  # Test Spamshield Form
  Scenario: Validator should calculate 92% chance of spam on submit
    Given I am on "/powermail/pi1/validation/misc/spamshield/active-mail-notify"
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

    Then I should see "Barbapappa"
    Then I should see "This is a text"
    Then I should see "test"
