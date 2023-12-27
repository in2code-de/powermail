# Features/Pi2/List.feature
@Pi2 @Pi2List
Feature: List

  @Pi2ListEntry
  Scenario: Add a new dummy Entry
    Given I am on "/powermail/pi2/standard/form"
    When I fill in "tx_powermail_pi1[field][string]" with "Daniel Boxhammer"
    When I fill in "tx_powermail_pi1[field][textarea]" with "Das ist ein Test"
    When I select "Sandra" from "tx_powermail_pi1[field][marker]"
    When I select "Alex" from "tx_powermail_pi1[field][selectmulti][]"
    When I additionally select "Olli" from "tx_powermail_pi1[field][selectmulti][]"
    When I check "tx_powermail_pi1[field][check][]"
    When I select "Olli" from "tx_powermail_pi1[field][radio]"
    And I press "Submit"
    And I press "Weiter"
    Then I should see "Daniel Boxhammer"

  @Pi2ListCheck
  Scenario: Check entries in List View
    Given I am on "/powermail/pi2/standard/list"
    Then I should see "Daniel Boxhammer"
    Then I should see "Das ist ein Test"
    Then I should see "Sandra"
    Then I should see "Olli"
    Then the sourcecode should contain '<li class="disabled">'
    Then the sourcecode should contain '<a href="#">Z</a>'
    Then the sourcecode should contain '<input class="btn btn-primary" type="submit" value="XLS">'

  @Pi2ListFilterEmpty
  Scenario: Check empty Filter over List View
    Given I am on "/powermail/pi2/standard/list"
    When I fill in "tx_powermail_pi2[filter][_all]" with "öoijasd908püuß980asdöijo"
    And I press "Jetzt Filtern"
    Then I should see "Keine Mails vorhanden"
    Then I should see "Bitte bearbeiten Sie Ihre Filter-Einstellungen."

  @Pi2ListFilter
  Scenario: Check empty Filter over List View
    Given I am on "/powermail/pi2/standard/list"
    When I fill in "tx_powermail_pi2[filter][_all]" with "Daniel"
    And I press "Jetzt Filtern"
    Then I should not see "Keine Mails vorhanden"
    Then I should not see "Bitte bearbeiten Sie Ihre Filter-Einstellungen."
    Then I should see "Daniel Boxhammer"
    Then I should see "Das ist ein Test"
    Then I should see "Olli"
