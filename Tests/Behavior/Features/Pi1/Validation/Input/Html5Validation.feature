# Features/Pi1/Validation/Html5JsPhpValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationInput @Pi1ValidationInputHtml5Validation
Feature: Html5JsPhpValidation

  @javascript @Pi1ValidationInputHtml5ValidationPhone
  Scenario: Check if phone validation are working correct
    Given I am on "/index.php?id=88"
    Then I should see "Email"
    Then I should see "Mandatory*"
    Then I should see "URL"
    Then I should see "Phone"
    And I press "Submit"
    Then I should not see "Success!"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"

    ####
    # Invalid Phone numbers
    ####
    Then I fill in "tx_powermail_pi1[field][phone]" with "a123546"
    And I press "Submit"
    Then I should not see "Success!"

    Then I fill in "tx_powermail_pi1[field][phone]" with "12(3)45"
    And I press "Submit"
    Then I should not see "Success!"

    Then I fill in "tx_powermail_pi1[field][phone]" with "ab cd ef"
    And I press "Submit"
    Then I should not see "Success!"

    Then I fill in "tx_powermail_pi1[field][phone]" with "0 123 456 7890"
    And I press "Submit"
    Then I should not see "Success!"

    Then I fill in "tx_powermail_pi1[field][phone]" with "+49 (0) 36 43/58 xx xx"
    And I press "Submit"
    Then I should not see "Success!"

    Then I fill in "tx_powermail_pi1[field][phone]" with "+3a"
    And I press "Submit"
    Then I should not see "Success!"

    Then I fill in "tx_powermail_pi1[field][phone]" with "0"
    And I press "Submit"
    Then I should not see "Success!"

    ####
    # Valid Phone Numbers
    ####
    Given I am on "/index.php?id=88"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"
    Then I fill in "tx_powermail_pi1[field][phone]" with "01234567890"
    And I press "Submit"
    Then I should see "Success!"

    Given I am on "/index.php?id=88"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"
    Then I fill in "tx_powermail_pi1[field][phone]" with "0123 4567890"
    And I press "Submit"
    Then I should see "Success!"

    Given I am on "/index.php?id=88"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"
    Then I fill in "tx_powermail_pi1[field][phone]" with "0123 456 789"
    And I press "Submit"
    Then I should see "Success!"

    Given I am on "/index.php?id=88"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"
    Then I fill in "tx_powermail_pi1[field][phone]" with "(0123) 45678 - 90"
    And I press "Submit"
    Then I should see "Success!"

    Given I am on "/index.php?id=88"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"
    Then I fill in "tx_powermail_pi1[field][phone]" with "0012 345 678 9012"
    And I press "Submit"
    Then I should see "Success!"

    Given I am on "/index.php?id=88"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"
    Then I fill in "tx_powermail_pi1[field][phone]" with "0012 (0)345 / 67890 - 12"
    And I press "Submit"
    Then I should see "Success!"

    Given I am on "/index.php?id=88"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"
    Then I fill in "tx_powermail_pi1[field][phone]" with "+123456789012"
    And I press "Submit"
    Then I should see "Success!"

    Given I am on "/index.php?id=88"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"
    Then I fill in "tx_powermail_pi1[field][phone]" with "+12 345 678 9012"
    And I press "Submit"
    Then I should see "Success!"

    Given I am on "/index.php?id=88"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"
    Then I fill in "tx_powermail_pi1[field][phone]" with "+12 3456 7890123"
    And I press "Submit"
    Then I should see "Success!"

    Given I am on "/index.php?id=88"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"
    Then I fill in "tx_powermail_pi1[field][phone]" with "+49 (0) 123 3456789"
    And I press "Submit"
    Then I should see "Success!"

    Given I am on "/index.php?id=88"
    Then I fill in "tx_powermail_pi1[field][mandatory]" with "aaa"
    Then I fill in "tx_powermail_pi1[field][phone]" with "+49 (0)123 / 34567 - 89"
    And I press "Submit"
    Then I should see "Success!"