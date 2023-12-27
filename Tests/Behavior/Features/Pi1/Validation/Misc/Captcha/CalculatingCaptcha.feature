# Features/Pi1/Validation/CaptchaValidation.feature
@Pi1 @Pi1Validation @Pi1ValidationMisc @Pi1ValidationMiscCaptcha @Pi1ValidationMiscCaptchaCalculatingCaptcha
Feature: CaptchaValidation
  Validation for powermail forms with a calculating captcha.

  # language_mode is default
  @Pi1ValidationMiscCaptchaCalculatingCaptchaL0
  Scenario: Searching for a Form with a captcha on PID 70
    Given I am on "/powermail/pi1/validation/misc/captcha/calculatingcaptcha/calculatingcaptcha-confirm"
    Then I should see "Email"
    Then I should see "Captcha"
    Then I should see an "#powermail_captchaimage" element
    Then the sourcecode should contain '<img src="/typo3temp/assets/tx_powermail/Captcha'

    When I fill in "tx_powermail_pi1[field][captcha]" with "anystring"
    And I press "Submit"

    Then I should see "Captcha: Falscher Captcha-Code eingegeben - bitte probieren Sie es nochmal!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "4"
    And I press "Submit"

    Then I should see "Captcha: Falscher Captcha-Code eingegeben - bitte probieren Sie es nochmal!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "3"
    And I press "Submit"

    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I should see "3"

  @Pi1ValidationMiscCaptchaCalculatingCaptchaL1
  Scenario: Searching for a Form with a captcha on PID 70
    Given I am on "/en/powermail/pi1/validation/misc/captcha/calculatingcaptcha/captcha-confirmation"
    Then I should see "Email"
    Then I should see "Captcha"
    Then I should see an "#powermail_captchaimage" element
    Then the sourcecode should contain '<img src="/typo3temp/assets/tx_powermail/Captcha'

    When I fill in "tx_powermail_pi1[field][captcha]" with "anystring"
    And I press "Submit"

    Then I should see "Wrong captcha code entered - please try again!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "4"
    And I press "Submit"

    Then I should see "Wrong captcha code entered - please try again!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "3"
    And I press "Submit"

    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I should see "3"


  # language_mode is strict
  @Pi1ValidationMiscCaptchaCalculatingCaptchaStrictL0
  Scenario: Searching for a Form with a captcha on PID 155
    Given I am on "/powermail/pi1/validation/misc/captcha/calculatingcaptcha/calculatingcaptcha-confirm-languagemodestrict"
    Then I should see "Email"
    Then I should see "Captcha"
    Then I should see an "#powermail_captchaimage" element
    Then the sourcecode should contain '<img src="/typo3temp/assets/tx_powermail/Captcha'

    When I fill in "tx_powermail_pi1[field][captcha]" with "anystring"
    And I press "Submit"

    Then I should see "Captcha: Falscher Captcha-Code eingegeben - bitte probieren Sie es nochmal!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "4"
    And I press "Submit"

    Then I should see "Captcha: Falscher Captcha-Code eingegeben - bitte probieren Sie es nochmal!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "3"
    And I press "Submit"

    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I should see "3"

  @Pi1ValidationMiscCaptchaCalculatingCaptchaStrictL1
  Scenario: Searching for a Form with a captcha on PID 155
    Given I am on "/en/powermail/pi1/validation/misc/captcha/calculatingcaptcha/calculatingcaptcha-confirm-languagemodestrict"
    Then I should see "Email"
    Then I should see "Captcha"
    Then I should see an "#powermail_captchaimage" element
    Then the sourcecode should contain '<img src="/typo3temp/assets/tx_powermail/Captcha'

    When I fill in "tx_powermail_pi1[field][captcha]" with "anystring"
    And I press "Submit"

    Then I should see "Wrong captcha code entered - please try again!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "4"
    And I press "Submit"

    Then I should see "Wrong captcha code entered - please try again!"

    When I fill in "tx_powermail_pi1[field][captcha]" with "3"
    And I press "Submit"

    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I should see "3"

  # Two captchas in one form
  @Pi1ValidationMiscCaptchaCalculatingCaptchaCalculatingCaptcha2
  Scenario: Test if two captchas work in one form on PID 166
    Given I am on "/powermail/pi1/validation/misc/captcha/calculatingcaptcha/calculatingcaptcha2"
    Then I should see "Email"
    Then I should see "captcha 1"
    Then I should see "captcha 2"
    Then I should see an "#powermail_field_captcha1" element
    Then I should see an "#powermail_field_captcha2" element
    Then the sourcecode should contain '<img src="/typo3temp/assets/tx_powermail/Captcha1951.png'
    Then the sourcecode should contain '<img src="/typo3temp/assets/tx_powermail/Captcha1952.png'

    When I fill in "tx_powermail_pi1[field][captcha1]" with "anystring"
    When I fill in "tx_powermail_pi1[field][captcha2]" with "7"
    And I press "Submit"

    Then I should see "Falscher Captcha-Code eingegeben - bitte probieren Sie es nochmal!"

    When I fill in "tx_powermail_pi1[field][captcha1]" with "3"
    When I fill in "tx_powermail_pi1[field][captcha2]" with "6"
    And I press "Submit"

    Then I should see "Falscher Captcha-Code eingegeben - bitte probieren Sie es nochmal!"

    When I fill in "tx_powermail_pi1[field][captcha1]" with "7"
    When I fill in "tx_powermail_pi1[field][captcha2]" with "6"
    And I press "Submit"

    Then I should see "Falscher Captcha-Code eingegeben - bitte probieren Sie es nochmal!"

    When I fill in "tx_powermail_pi1[field][captcha1]" with "7"
    When I fill in "tx_powermail_pi1[field][captcha2]" with "7"
    And I press "Submit"

    Then I should not see "Captcha: Falscher Captcha-Code eingegeben - bitte probieren Sie es nochmal!"
    Then I should see "7"

  # Two forms with one captcha per form
  @Pi1ValidationMiscCaptchaCalculatingCaptcha2Forms
  Scenario: Test if two forms with captcha are working on PID 164
    Given I am on "/powermail/pi1/validation/misc/captcha/calculatingcaptcha/2-forms"
    Then I should see "Email"
    Then I should see "Email2"
    Then I should see "Captcha"
    Then I should see "Captcha2"
    Then I should see an "#powermail_field_captcha" element
    Then I should see an "#powermail_field_captcha2" element
    Then the sourcecode should contain '<img src="/typo3temp/assets/tx_powermail/Captcha1587.png'
    Then the sourcecode should contain '<img src="/typo3temp/assets/tx_powermail/Captcha1948.png'

    When I fill in "tx_powermail_pi1[field][captcha]" with "1"
    And I press "Submit"

    Then I should see "Sind diese Werte richtig?"

    When I fill in "tx_powermail_pi1[field][captcha2]" with "1"
    And I press "Submit2"

    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I should see "1"

  # Two forms with one captcha per form with AJAX
  @javascript @Pi1ValidationMiscCaptchaCalculatingCaptcha2FormsAjax
  Scenario: Test if two forms with captcha are working together with AJAX on PID 167
    Given I am on "/powermail/pi1/validation/misc/captcha/calculatingcaptcha/2-forms-ajax"
    Then I should see "Email"
    Then I should see "Email2"
    Then I should see "Captcha"
    Then I should see "Captcha2"
    Then I should see an "#powermail_field_captcha" element
    Then I should see an "#powermail_field_captcha2" element
    Then the sourcecode should contain 'src="/typo3temp/assets/tx_powermail/Captcha1587.png'
    Then the sourcecode should contain 'src="/typo3temp/assets/tx_powermail/Captcha1948.png'

    When I fill in "tx_powermail_pi1[field][captcha]" with "25"
    And I press "Submit"
    And I wait "a few" seconds

    Then I should see "Sind diese Werte richtig?"

    When I fill in "tx_powermail_pi1[field][captcha2]" with "25"
    And I press "Submit"
    And I wait "a few" seconds

    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    Then I should see "1"
