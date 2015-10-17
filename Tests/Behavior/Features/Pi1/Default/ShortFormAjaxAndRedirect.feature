# Features/Pi1/Default/ShortFormAjaxAndRedirect.feature
@Pi1 @Pi1Default @Pi1DefaultShortFormAjaxAndRedirect
Feature: AllFields
  Check if redirect works correct with AJAX submit

  @javascript
  Scenario: Check if redirect works correct with AJAX submit
    Given I am on "/index.php?id=163"
    Then I should see "Redirect to page \"Welcome\""
    And I press "Jetzt Absenden"
    And I wait "a few" seconds

    Then I should see "Sind diese Eingaben korrekt"
    And I press "Weiter"
    And I wait "a few" seconds

    Then I should see "Willkommen zum powermail Testparcour"
