# Features/Pi2/List.feature
@Pi2 @Pi2List
Feature: List

  @Pi2ListEntry
  Scenario: Add a new dummy Entry
    Given I am on "/index.php?id=40"
    When I fill in "tx_powermail_pi1[field][string]" with "Andy Kräuter"
    When I fill in "tx_powermail_pi1[field][textarea]" with "Das ist ein Test"
    When I select "Sandra" from "tx_powermail_pi1[field][marker]"
    When I select "Alex" from "tx_powermail_pi1[field][selectmulti][]"
    When I additionally select "Olli" from "tx_powermail_pi1[field][selectmulti][]"
    When I check "tx_powermail_pi1[field][marker_01][]"
    When I select "Silke" from "tx_powermail_pi1[field][radio]"
    And I press "Submit"
    And I press "Weiter"
    Then I should see "Andy Kräuter"

  @Pi2ListCheck
  Scenario: Check entries in List View
    Given I am on "/index.php?id=30"
    Then I should see "Andy Kräuter"
    Then I should see "Das ist ein Test"
    Then I should see "Sandra"
    Then I should see "Silke"
    Then the sourcecode should contain '<span class="abc">Z</span>'
    Then the sourcecode should contain 'powermail_frontend_export_icon'

  @Pi2ListFilterEmpty
  Scenario: Check empty Filter over List View
    Given I am on "/index.php?id=30"
    When I fill in "tx_powermail_pi2[filter][_all]" with "öoijasd908püuß980asdöijo"
    And I press "Jetzt Filtern"
    Then I should see "Keine Mails gefunden"
    Then I should see "Bitte passen Sie Ihre Filtereinstellungen an"

  @Pi2ListFilter
  Scenario: Check empty Filter over List View
    Given I am on "/index.php?id=30"
    When I fill in "tx_powermail_pi2[filter][_all]" with "Andy"
    And I press "Jetzt Filtern"
    Then I should not see "Keine Mails gefunden"
    Then I should not see "Bitte passen Sie Ihre Filtereinstellungen an"
    Then I should see "Andy Kräuter"
    Then I should see "Das ist ein Test"
    Then I should see "Silke"