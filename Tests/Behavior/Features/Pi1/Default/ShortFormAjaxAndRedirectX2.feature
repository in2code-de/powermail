# Features/Pi1/Default/ShortFormAjaxAndRedirectX2.feature
@Pi1 @Pi1Default @Pi1DefaultShortFormAjaxAndRedirectX2
Feature: AllFields
  Check if redirect works correct with AJAX submit, even if there are two forms on one page

  @javascript
  Scenario: Check redirect URI for form 1
    Given I am on "/powermail/pi1/default/shortform-ajax-redirect/2x-ajax-redirect"
    Then I should see "Redirect to page \"Welcome\""
    And I press "Jetzt Absenden"
    And I wait "a few" seconds

    Then I should see "Willkommen zum powermail Testparcour"

  @javascript
  Scenario: Check redirect URI for form 2
    Given I am on "/powermail/pi1/default/shortform-ajax-redirect/2x-ajax-redirect"
    Then I should see "Redirect to page \"All Fields\""
    And I scroll ".powermail_fieldwrap_marker_02" into view
    And I press "Submit2"
    And I wait "a few" seconds
    When I fill in "tx_powermail_pi1[field][marker_01]" with "3"
    And I press "Submit2"
    And I wait "a few" seconds
    Then I should see "Sind diese Eingaben korrekt"
    And I press "Weiter"
    And I wait "a few" seconds

    Then I should see "All Fields"
