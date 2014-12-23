# Features/Pi1/Validation/CaptchaValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationMisc @Pi1ValidationMiscCaptchaValidation
Feature: CaptchaValidation
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # Test Captcha Form
  Scenario: Searching for a Form with a captcha
    Given I am on "/index.php?id=70"
    Then I should see "Email"
    Then I should see "Captcha"
    Then I should see an "#powermail_captchaimage" element
    Then the sourcecode should contain '<img src="typo3temp/tx_powermail/CalculatingCaptcha.png'

    When I fill in "tx_powermail_pi1[field][captcha]" with "4"
    And I press "Submit"

    Then I should see "Falscher Captcha Code eingetragen, bitte erneut versuchen!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "3"
    And I press "Submit"

    Then I should see "alex@in2code.de"
    Then I should see "3"