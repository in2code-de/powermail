# Features/Pi1/Prefill/PreselectedOptions.feature
@Pi1 @Pi1Prefill @Pi1PrefillPreselectedOptions
Feature: PreselectedOptions

  # Field "Check" with the options "red|red|*" and "blue|blue|*"
  # Preselected options (*) must not override the values a visitor has already submitted
  # Related: in2code-de/powermail#1397

  @javascript
  Scenario: Preselected options are checked on first view
    Given I am on "/powermail/pi1/upload/upload"
    Then the "powermail_field_e_mail_1" checkbox should be checked
    Then the "powermail_field_e_mail_2" checkbox should be checked

  @javascript
  Scenario: Unchecked preselected option stays unchecked when coming back from confirmation view
    Given I am on "/powermail/pi1/upload/upload"
    When I uncheck "powermail_field_e_mail_2"
    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt?"
    And I press "Zurück"

    Then the "powermail_field_e_mail_1" checkbox should be checked
    Then the "powermail_field_e_mail_2" checkbox should not be checked

  @javascript
  Scenario: All unchecked options stay unchecked when coming back from confirmation view
    Given I am on "/powermail/pi1/upload/upload"
    When I uncheck "powermail_field_e_mail_1"
    When I uncheck "powermail_field_e_mail_2"
    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt?"
    And I press "Zurück"

    Then the "powermail_field_e_mail_1" checkbox should not be checked
    Then the "powermail_field_e_mail_2" checkbox should not be checked

  @javascript
  Scenario: Unchecked preselected option stays unchecked after a serverside validation error
    Given I am on "/powermail/pi1/upload/upload"
    When I disable the clientside validation of the form ".powermail_form_9"
    When I fill in "tx_powermail_pi1[field][name]" with ""
    When I uncheck "powermail_field_e_mail_1"
    And I press "Submit"

    Then I should see "Dieses Feld muss ausgefüllt werden!"
    Then the "powermail_field_e_mail_1" checkbox should not be checked
    Then the "powermail_field_e_mail_2" checkbox should be checked
