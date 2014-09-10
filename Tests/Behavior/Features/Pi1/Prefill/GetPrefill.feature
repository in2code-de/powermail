# Features/Pi1/Default/GetPrefill.feature
Feature: GetPrefill
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  # id=17 will be redirected to id=19 with some GET-Parameters
  @Pi1PrefillGetPrefill
  Scenario: Check if Form can be prefilled with GET-Parameters
    Given I am on "/index.php?id=17"
    Then the "tx_powermail_pi1[field][input]" field should contain "Silke Kellner"
    Then the "tx_powermail_pi1[field][marker]" field should contain "Der Test"
    Then the sourcecode should contain '<option value="green" selected="selected">green</option>'
    Then the sourcecode should contain '<option value="brown" selected="selected">brown</option>'
    Then the sourcecode should contain '<option value="black" selected="selected">black</option>'
    Then the sourcecode should contain 'name="tx_powermail_pi1[field][marker_03][]" value="black" checked="checked" />'
    Then the sourcecode should contain 'name="tx_powermail_pi1[field][marker_03][]" value="pink" checked="checked" />'
    Then the sourcecode should contain 'name="tx_powermail_pi1[field][marker_04]" value="pink" checked="checked" />'
