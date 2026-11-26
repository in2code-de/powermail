# Features/Pi1/Validation/SpamShieldValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationMisc @Pi1ValidationMiscSpamShield @Pi1ValidationMiscSpamShieldActive
Feature: Active

  # Test Spamshield Form
  # Detected spam (92% and 90% here) is redirected to a new and empty form, the spam factor is only logged
  Scenario: Spam submits are redirected to an empty form, a clean submit passes
    Given I am on "/powermail/pi1/validation/misc/spamshield/active-mail-notify"
    Then I should see "Name"
    Then I should see "E-Mail"
    Then I should see "Text"
    Then the "tx_powermail_pi1[field][name]" field should contain "Viagra"
    Then the "tx_powermail_pi1[field][e_mail]" field should contain "Viagra"

    And I press "Submit"

    Then the "tx_powermail_pi1[field][e_mail]" field should contain "Viagra"
    Then I should not see "Spam-Wahrscheinlichkeit in dieser Nachricht!"

    When I fill in "tx_powermail_pi1[field][e_mail]" with "test"
    And I press "Submit"

    Then the "tx_powermail_pi1[field][e_mail]" field should contain "Viagra"

    When I fill in "tx_powermail_pi1[field][e_mail]" with "test"
    When I fill in "tx_powermail_pi1[field][text]" with "This is a text"
    When I fill in "tx_powermail_pi1[field][name]" with "Barbapappa"

    And I press "Submit"

    Then I should see a ".powermail_all_value" element
    Then I should see "Barbapappa"
    Then I should see "This is a text"
    Then I should see "test"
