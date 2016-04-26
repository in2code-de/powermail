# Features/Pi1/Validation/CaptchaValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationMisc @Pi1ValidationMiscCaptcha @Pi1ValidationMiscCaptchaCalculatingCaptcha
Feature: CaptchaValidation
  Validation for powermail forms with a calculating captcha.

  # language_mode is default
  @Pi1ValidationMiscCaptchaCalculatingCaptchaL0
  Scenario: Searching for a Form with a captcha
    Given I am on "/index.php?id=70"
    Then I should see "Email"
    Then I should see "Captcha"
    Then I should see an "#powermail_captchaimage" element
    Then the sourcecode should contain '<img src="typo3temp/tx_powermail/Captcha'

    When I fill in "tx_powermail_pi1[field][captcha]" with "anystring"
    And I press "Submit"

    Then I should see "Falscher Captcha Code eingetragen, bitte erneut versuchen!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "4"
    And I press "Submit"

    Then I should see "Falscher Captcha Code eingetragen, bitte erneut versuchen!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "3"
    And I press "Submit"

    Then I should see "alex@in2code.de"
    Then I should see "3"

  @Pi1ValidationMiscCaptchaCalculatingCaptchaL1
  Scenario: Searching for a Form with a captcha
    Given I am on "/index.php?id=70&L=1"
    Then I should see "Email"
    Then I should see "Captcha"
    Then I should see an "#powermail_captchaimage" element
    Then the sourcecode should contain '<img src="typo3temp/tx_powermail/Captcha'

    When I fill in "tx_powermail_pi1[field][captcha]" with "anystring"
    And I press "Submit"

    Then I should see "Wrong captcha code entered - please try again!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "4"
    And I press "Submit"

    Then I should see "Wrong captcha code entered - please try again!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "3"
    And I press "Submit"

    Then I should see "alex@in2code.de"
    Then I should see "3"


  # language_mode is strict
  @Pi1ValidationMiscCaptchaCalculatingCaptchaStrictL0
  Scenario: Searching for a Form with a captcha
    Given I am on "/index.php?id=155"
    Then I should see "Email"
    Then I should see "Captcha"
    Then I should see an "#powermail_captchaimage" element
    Then the sourcecode should contain '<img src="typo3temp/tx_powermail/Captcha'

    When I fill in "tx_powermail_pi1[field][captcha]" with "anystring"
    And I press "Submit"

    Then I should see "Falscher Captcha Code eingetragen, bitte erneut versuchen!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "4"
    And I press "Submit"

    Then I should see "Falscher Captcha Code eingetragen, bitte erneut versuchen!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "3"
    And I press "Submit"

    Then I should see "alex@in2code.de"
    Then I should see "3"

  @Pi1ValidationMiscCaptchaCalculatingCaptchaStrictL1
  Scenario: Searching for a Form with a captcha
    Given I am on "/index.php?id=155&L=1"
    Then I should see "Email"
    Then I should see "Captcha"
    Then I should see an "#powermail_captchaimage" element
    Then the sourcecode should contain '<img src="typo3temp/tx_powermail/Captcha'

    When I fill in "tx_powermail_pi1[field][captcha]" with "anystring"
    And I press "Submit"

    Then I should see "Wrong captcha code entered - please try again!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "4"
    And I press "Submit"

    Then I should see "Wrong captcha code entered - please try again!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "3"
    And I press "Submit"

    Then I should see "alex@in2code.de"
    Then I should see "3"

  # Two captchas in one form
  @Pi1ValidationMiscCaptchaCalculatingCaptchaCalculatingCaptcha2
  Scenario: Test if two captchas work in one form
    Given I am on "/index.php?id=166"
    Then I should see "Email"
    Then I should see "captcha 1"
    Then I should see "captcha 2"
    Then I should see an "#powermail_field_captcha1" element
    Then I should see an "#powermail_field_captcha2" element
    Then the sourcecode should contain '<img src="typo3temp/tx_powermail/Captcha1951.png'
    Then the sourcecode should contain '<img src="typo3temp/tx_powermail/Captcha1952.png'

    When I fill in "tx_powermail_pi1[field][captcha1]" with "anystring"
    When I fill in "tx_powermail_pi1[field][captcha2]" with "7"
    And I press "Submit"

    Then I should see "Falscher Captcha Code eingetragen"

    When I fill in "tx_powermail_pi1[field][captcha1]" with "3"
    When I fill in "tx_powermail_pi1[field][captcha2]" with "6"
    And I press "Submit"

    Then I should see "Falscher Captcha Code eingetragen"

    When I fill in "tx_powermail_pi1[field][captcha1]" with "7"
    When I fill in "tx_powermail_pi1[field][captcha2]" with "6"
    And I press "Submit"

    Then I should see "Falscher Captcha Code eingetragen"

    When I fill in "tx_powermail_pi1[field][captcha1]" with "7"
    When I fill in "tx_powermail_pi1[field][captcha2]" with "7"
    And I press "Submit"

    Then I should not see "Falscher Captcha Code eingetragen"
    Then I should see "7"

  # Two forms with one captcha per form
  @Pi1ValidationMiscCaptchaCalculatingCaptcha2Forms
  Scenario: Test if two forms with captcha are working
    Given I am on "/index.php?id=164"
    Then I should see "Email"
    Then I should see "Email2"
    Then I should see "Captcha"
    Then I should see "Captcha2"
    Then I should see an "#powermail_field_captcha" element
    Then I should see an "#powermail_field_captcha2" element
    Then the sourcecode should contain '<img src="typo3temp/tx_powermail/Captcha1587.png'
    Then the sourcecode should contain '<img src="typo3temp/tx_powermail/Captcha1948.png'

    When I fill in "tx_powermail_pi1[field][captcha]" with "1"
    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt"

    When I fill in "tx_powermail_pi1[field][captcha2]" with "1"
    And I press "Submit"

    Then I should see "alex@in2code.de"
    Then I should see "1"

  # Two forms with one captcha per form with AJAX
  @javascript @Pi1ValidationMiscCaptchaCalculatingCaptcha2FormsAjax
  Scenario: Test if two forms with captcha are working together with AJAX
    Given I am on "/index.php?id=167"
    Then I should see "Email"
    Then I should see "Email2"
    Then I should see "Captcha"
    Then I should see "Captcha2"
    Then I should see an "#powermail_field_captcha" element
    Then I should see an "#powermail_field_captcha2" element
    Then the sourcecode should contain 'src="typo3temp/tx_powermail/Captcha1587.png'
    Then the sourcecode should contain 'src="typo3temp/tx_powermail/Captcha1948.png'

    When I fill in "tx_powermail_pi1[field][captcha]" with "25"
    And I press "Submit"
    And I wait "a few" seconds

    Then I should see "Sind diese Eingaben korrekt"

    When I fill in "tx_powermail_pi1[field][captcha2]" with "25"
    And I press "Submit"
    And I wait "a few" seconds

    Then I should see "alex@in2code.de"
    Then I should see "1"
