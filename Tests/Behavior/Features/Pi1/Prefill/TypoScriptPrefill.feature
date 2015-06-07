# Features/Pi1/Default/TypoScriptPrefill.feature
@Pi1 @Pi1Prefill @Pi1PrefillTypoScriptPrefill
Feature: TypoScriptPrefill

  Scenario: Check if Form can be prefilled with TypoScript Configuration
    Given I am on "/index.php?id=16"
    Then I should see "Input"
    Then I should see "Textarea"
    Then I should see "Select"
    Then I should see "Select Multi"
    Then I should see "Check"
    Then I should see "Radio"
    Then the "tx_powermail_pi1[field][input]" field should contain "Alex Kellner"
    Then the sourcecode should contain 'name="tx_powermail_pi1[field][marker]">Das'
    Then the sourcecode should contain '<option value="green" selected="selected">green</option>'
    Then the sourcecode should contain '<option value="brown" selected="selected">brown</option>'
    Then the sourcecode should contain '<option value="black" selected="selected">black</option>'
    Then the sourcecode should contain 'name="tx_powermail_pi1[field][marker_03][]" value="black" checked="checked" />'
    Then the sourcecode should contain 'name="tx_powermail_pi1[field][marker_03][]" value="pink" checked="checked" />'
    Then the sourcecode should contain 'name="tx_powermail_pi1[field][marker_04]" value="pink" checked="checked" />'
